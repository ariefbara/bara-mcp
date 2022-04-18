<?php

namespace App\Http\Controllers\Client;

use Client\Application\Service\Client\CancelProgramRegistration;
use Client\Application\Service\Client\RegisterToProgram;
use Client\Domain\Model\Client;
use Client\Domain\Model\Client\ProgramRegistration;
use Config\EventList;
use Firm\Application\Listener\ListeningToProgramRegistrationFromClient;
use Firm\Application\Service\Firm\Program\ExecuteTask;
use Firm\Domain\Model\Firm\Client as Client2;
use Firm\Domain\Model\Firm\Program as Program2;
use Firm\Domain\Task\InProgram\ReceiveApplicationFromClient;
use Query\Application\Service\Firm\Client\ViewProgramRegistration;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Query\Domain\Model\Firm\FirmFileInfo;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Application\Event\Dispatcher;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;
use SharedContext\Domain\Model\Firm\Program;

class ProgramRegistrationController extends ClientBaseController
{

    public function register()
    {
        $addClientParticipantTask = new \Client\Domain\Task\AddClientParticipant(
                $this->em->getRepository(Client\ClientParticipant::class),
                $this->em->getRepository(\Client\Domain\DependencyModel\Firm\Program\Participant::class));
        $addClientRegistrantTask = new \Client\Domain\Task\AddClientRegistrant(
                $this->em->getRepository(Client\ClientRegistrant::class),
                $this->em->getRepository(\Client\Domain\DependencyModel\Firm\Program\Registrant::class));
        
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $transactionalSession->executeAtomically(function () use($addClientParticipantTask, $addClientRegistrantTask) {
            $programRepository = $this->em->getRepository(\Client\Domain\DependencyModel\Firm\Program::class);
            $dispatcher = new AdvanceDispatcher();

            $executeProgramTaskService = new ExecuteTask($this->em->getRepository(Program2::class));
            $receiveApplicationFromClientTask = new ReceiveApplicationFromClient(
                    $this->em->getRepository(Client2::class), $dispatcher);
            $listeningToProgramRegistrationFromClient = new ListeningToProgramRegistrationFromClient(
                    $executeProgramTaskService, $receiveApplicationFromClientTask);
            $dispatcher->addImmediateListener(
                    EventList::CLIENT_HAS_APPLIED_TO_PROGRAM, $listeningToProgramRegistrationFromClient);

            $clientExecuteTaskService = new \Client\Application\Service\ExecuteTask(
                    $this->em->getRepository(Client::class));
            $acknowledgeParticipationReceived = new \Client\Application\Listener\AcknowledgeParticipationReceived(
                    $clientExecuteTaskService, $addClientParticipantTask);

            $acknowledgeRegistrationReceived = new \Client\Application\Listener\AcknowledgeRegistrationReceived(
                    $clientExecuteTaskService, $addClientRegistrantTask);

            $dispatcher->addPostponedListener(EventList::PROGRAM_APPLICATION_RECEIVED, $acknowledgeParticipationReceived);
            $dispatcher->addPostponedListener(EventList::PROGRAM_PARTICIPATION_ACCEPTED, $acknowledgeParticipationReceived);
            $dispatcher->addPostponedListener(EventList::PROGRAM_APPLICATION_RECEIVED, $acknowledgeRegistrationReceived);
            $dispatcher->addPostponedListener(EventList::PROGRAM_REGISTRATION_RECEIVED, $acknowledgeRegistrationReceived);

            $task = new \Client\Domain\Task\ApplyProgram($programRepository, $dispatcher);
            $programId = $this->stripTagsInputRequest('programId');
            $this->executeClientTask($task, $programId);
            $dispatcher->finalize();
        });
        $result = ['registrant' => null, 'participant' => null];
        if ($clientRegistrantId = $addClientRegistrantTask->addedClientRegistrantId) {
            $viewService = $this->buildViewService();
            $clientRegistrant = $viewService->showById($this->firmId(), $this->clientId(), $clientRegistrantId);
            $result['registrant'] = [
                'id' => $clientRegistrant->getId(),
                'status' => $clientRegistrant->getStatus(),
                'registeredTime' => $clientRegistrant->getRegisteredTimeString(),
            ];
        } elseif ($clientParticipantId = $addClientParticipantTask->addedClientParticipantId) {
            $clientParticipantViewService = new \Query\Application\Service\Firm\Client\ViewProgramParticipation($this->em->getRepository(\Query\Domain\Model\Firm\Client\ClientParticipant::class));
            $clientparticipant = $clientParticipantViewService->showById($this->firmId(), $this->clientId(), $clientParticipantId);
            $result['participant'] = [
                'id' => $clientparticipant->getId(),
                'enrolledTime' => $clientparticipant->getEnrolledTimeString(),
            ];
        }
        return $this->commandCreatedResponse($result);
    }

//    public function register()
//    {
//        $service = $this->buildRegisterService();
//        
//        $firmId = $this->firmId();
//        $clientId = $this->clientId();
//        $programId = $this->stripTagsInputRequest('programId');
//        
//        $programRegistrationId = $service->execute($firmId, $clientId, $programId);
//        
//        $viewService = $this->buildViewService();
//        $programRegistration = $viewService->showById($this->firmId(), $this->clientId(), $programRegistrationId);
//        return $this->commandCreatedResponse($this->arrayDataOfProgramRegistration($programRegistration));
//    }

    public function cancel($programRegistrationId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $programRegistrationId);
        return $this->commandOkResponse();
    }

    public function show($programRegistrationId)
    {
        $service = $this->buildViewService();
        $programRegistration = $service->showById($this->firmId(), $this->clientId(), $programRegistrationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramRegistration($programRegistration));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $concludedStatus = $this->filterBooleanOfQueryRequest('concludedStatus');
        $programRegistrations = $service->showAll(
                $this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize(), $concludedStatus);

        $result = [];
        $result['total'] = count($programRegistrations);
        foreach ($programRegistrations as $programRegistration) {
            $result['list'][] = $this->arrayDataOfProgramRegistration($programRegistration);
        }

        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfProgramRegistration(ClientRegistrant $programRegistration): array
    {
        return [
            "id" => $programRegistration->getId(),
            "program" => [
                "id" => $programRegistration->getProgram()->getId(),
                "name" => $programRegistration->getProgram()->getName(),
                "hasProfileForm" => $programRegistration->getProgram()->hasProfileForm(),
                "programType" => $programRegistration->getProgram()->getProgramTypeValue(),
                "illustration" => $this->arrayDataOfIllustration($programRegistration->getProgram()->getIllustration()),
            ],
            "registeredTime" => $programRegistration->getRegisteredTimeString(),
            "concluded" => $programRegistration->isConcluded(),
            "note" => $programRegistration->getNote(),
        ];
    }

    protected function arrayDataOfIllustration(?FirmFileInfo $illustration): ?array
    {
        return empty($illustration) ? null : [
            "id" => $illustration->getId(),
            "url" => $illustration->getFullyQualifiedFileName(),
        ];
    }

    protected function buildRegisterService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        $clientRepository = $this->em->getRepository(Client::class);
        $programRepository = $this->em->getRepository(Program::class);
        $dispatcher = new Dispatcher();
        return new RegisterToProgram($programRegistrationRepository, $clientRepository, $programRepository, $dispatcher);
    }

    protected function buildCancelService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        return new CancelProgramRegistration($programRegistrationRepository);
    }

    protected function buildViewService()
    {
        $programRegistrationRepository = $this->em->getRepository(ClientRegistrant::class);
        return new ViewProgramRegistration($programRegistrationRepository);
    }

}
