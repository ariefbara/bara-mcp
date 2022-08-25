<?php

namespace App\Http\Controllers\Client;

use Client\Application\Service\Client\CancelProgramRegistration;
use Client\Application\Service\Client\RegisterToProgram;
use Client\Domain\Model\Client;
use Client\Domain\Model\Client\ProgramRegistration;
use Config\EventList;
use Firm\Application\Listener\AcceptProgramApplicationFromClient;
use Firm\Application\Listener\GenerateClientRegistrantInvoice;
use Firm\Domain\Model\Firm\Client as Client2;
use Firm\Domain\Model\Firm\Program as Program2;
use Query\Application\Service\Firm\Client\ViewProgramParticipation;
use Query\Application\Service\Firm\Client\ViewProgramRegistration;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Client\ClientRegistrant;
use Query\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Model\Firm\Program as Program3;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantInvoice;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Application\Event\Dispatcher;
use Resources\Application\Listener\SpyEntityCreation;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;
use SharedContext\Domain\Model\Firm\Program;
use SharedContext\Infrastructure\Xendit\XenditPaymentGateway;

class ProgramRegistrationController extends ClientBaseController
{

    public function register()
    {
        $dispatcher = new AdvanceDispatcher();
        
        $programRepository = $this->em->getRepository(Program2::class);
        $clientRepository = $this->em->getRepository(Client2::class);
        $acceptProgramApplicationFromClient = new AcceptProgramApplicationFromClient($programRepository, $clientRepository, $dispatcher);
        $dispatcher->addImmediateListener(
                EventList::CLIENT_HAS_APPLIED_TO_PROGRAM, $acceptProgramApplicationFromClient);
        
        $spyRegistrantCreation = new SpyEntityCreation();
        $dispatcher->addPostponedListener(EventList::PROGRAM_REGISTRATION_RECEIVED, $spyRegistrantCreation);
        $dispatcher->addPostponedListener(EventList::SETTLEMENT_REQUIRED, $spyRegistrantCreation);
        
        $clientRegistrantRepository = $this->em->getRepository(Client2\ClientRegistrant::class);
        $paymentGateway = new XenditPaymentGateway();
        $generateClientRegistrantInvoice = new GenerateClientRegistrantInvoice($clientRegistrantRepository, $paymentGateway);
        $dispatcher->addAsynchronousListener(EventList::SETTLEMENT_REQUIRED, $generateClientRegistrantInvoice);
        
        $spyParticipantCreation = new SpyEntityCreation();
        $dispatcher->addPostponedListener(EventList::PROGRAM_PARTICIPATION_ACCEPTED, $spyParticipantCreation);
        
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new \Client\Application\Service\ExecuteTask($clientRepository);
        
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $transactionalSession->executeAtomically(function () use ($service, $dispatcher) {
            $task = new \Client\Domain\Task\ApplyProgram($dispatcher);
            $programId = $this->request->input('programId');
            $service->execute($this->clientId(), $task, $programId);
            
            $dispatcher->finalize();
        });
        $dispatcher->finalizeAsynchronous();
        
        if ($registrantId = $spyRegistrantCreation->getEntityId()) {
            $clientRegistrant = $this->buildViewService()->showById($this->firmId(), $this->clientId(), $registrantId);
            $result = $this->arrayDataOfClientRegistrant($clientRegistrant);
        } elseif ($participantId = $spyParticipantCreation->getEntityId()) {
            $clientParticipant = $this->em->getRepository(ClientParticipant::class);
            $clientParticipant = (new ViewProgramParticipation($clientParticipant))
                    ->showById($this->firmId(), $this->clientId(), $participantId);
            $result = $this->arrayDataOfClientParticipant($clientParticipant);
        }
        return $this->commandCreatedResponse($result);
        
    }

//    public function cancel($programRegistrationId)
//    {
//        $clientRepository = $this->em->getRepository(Client::class);
//        $clientRegistrantRepository = $this->em->getRepository(Client\ClientRegistrant::class);
//        $task = new \Client\Domain\Task\CancelRegistration($clientRegistrantRepository);
//        (new \Client\Application\Service\ExecuteTask($clientRepository))
//                ->execute($this->clientId(), $task, $programRegistrationId);
//    }

    public function show($programRegistrationId)
    {
        $service = $this->buildViewService();
        $programRegistration = $service->showById($this->firmId(), $this->clientId(), $programRegistrationId);
        return $this->singleQueryResponse($this->arrayDataOfClientRegistrant($programRegistration));
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
            $result['list'][] = $this->arrayDataOfClientRegistrant($programRegistration);
        }

        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfClientRegistrant(ClientRegistrant $clientRegistrant): array
    {
        return [
            "id" => $clientRegistrant->getId(),
            "registeredTime" => $clientRegistrant->getRegisteredTimeString(),
            'status' => $clientRegistrant->getStatus(),
            'programSnapshot' => [
                "price" => $clientRegistrant->getProgramSnapshot()->getPrice(),
                "autoAccept" => $clientRegistrant->getProgramSnapshot()->isAutoAccept(),
            ],
            "program" => $this->arrayDataOfProgram($clientRegistrant->getProgram()),
            'invoice' => $clientRegistrant->getRegistrantInvoice() ? 
                $this->arrayDataOfRegistrantInvoice($clientRegistrant->getRegistrantInvoice()) : null,
        ];
    }
    protected function arrayDataOfClientParticipant(ClientParticipant $clientParticipant): array
    {
        return [
            "id" => $clientParticipant->getId(),
            "enrolledTime" => $clientParticipant->getEnrolledTimeString(),
            "note" => $clientParticipant->getNote(),
            "active" => $clientParticipant->isActive(),
            "program" => $this->arrayDataOfProgram($clientParticipant->getProgram()),
        ];
    }
    protected function arrayDataOfProgram(Program3 $program): array
    {
        $sponsors = [];
        foreach ($program->iterateActiveSponsort() as $sponsor) {
            $logo = empty($sponsor->getLogo()) ? null : [
                "id" => $sponsor->getLogo()->getId(),
                "url" => $sponsor->getLogo()->getFullyQualifiedFileName(),
            ];
            $sponsors[] = [
                "id" => $sponsor->getId(),
                "name" => $sponsor->getName(),
                "website" => $sponsor->getWebsite(),
                "logo" => $logo,
            ];
        }
        return [
            "id" => $program->getId(),
            "name" =>  $program->getName(),
            "removed" =>  $program->isRemoved(),
            "sponsors" => $sponsors,
        ];
    }
    protected function arrayDataOfRegistrantInvoice(RegistrantInvoice $registrantInvoice): array
    {
        return [
            'issuedTime' => $registrantInvoice->getIssuedTimeString(),
            'expiredTime' => $registrantInvoice->getExpiredTimeString(),
            'paymentLink' => $registrantInvoice->getPaymentLink(),
            'settled' => $registrantInvoice->isSettled(),
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
