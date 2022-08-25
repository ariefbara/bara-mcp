<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Config\EventList;
use Firm\Application\Listener\AddClientParticipant;
use Firm\Application\Listener\AddTeamParticipant;
use Firm\Application\Listener\GenerateClientRegistrantInvoice;
use Firm\Application\Listener\GenerateTeamRegistrantInvoice;
use Firm\Application\Service\Coordinator\ExecuteProgramTask;
use Firm\Domain\Model\Firm\Client\ClientRegistrant as ClientRegistrant2;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Registrant as Registrant2;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Firm\Domain\Task\InProgram\AcceptRegistrant;
use Firm\Domain\Task\InProgram\RejectRegistrant;
use Query\Application\Service\Firm\Program\ViewRegistrant;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Registrant;
use Query\Domain\Model\Firm\Team;
use Query\Domain\Model\User;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;
use SharedContext\Infrastructure\Xendit\XenditPaymentGateway;

class RegistrantController extends AsProgramCoordinatorBaseController
{

    public function accept($programId, $registrantId)
    {

        $dispatcher = new AdvanceDispatcher();

        $paymentGateway = new XenditPaymentGateway();
        
        $clientRegistrantRepository = $this->em->getRepository(ClientRegistrant2::class);
        $generateClientRegistrantInvoice = new GenerateClientRegistrantInvoice($clientRegistrantRepository, $paymentGateway);
        $dispatcher->addPostponedListener(EventList::SETTLEMENT_REQUIRED, $generateClientRegistrantInvoice );
        
        $teamRegistrantRepository = $this->em->getRepository(TeamRegistrant::class);
        $generateTeamRegistrantInvoice = new GenerateTeamRegistrantInvoice($teamRegistrantRepository, $paymentGateway);
        $dispatcher->addPostponedListener(EventList::SETTLEMENT_REQUIRED, $generateTeamRegistrantInvoice);
        
        $addClientParticipant = new AddClientParticipant($clientRegistrantRepository);
        $addTeamParticipant = new AddTeamParticipant($teamRegistrantRepository);
        $dispatcher->addPostponedListener(EventList::PROGRAM_PARTICIPATION_ACCEPTED, $addClientParticipant);
        $dispatcher->addPostponedListener(EventList::PROGRAM_PARTICIPATION_ACCEPTED, $addTeamParticipant);

        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $transactionalSession->executeAtomically(function () use ($dispatcher, $programId, $registrantId) {
            $registrantRepository = $this->em->getRepository(Registrant2::class);
            $task = new AcceptRegistrant($registrantRepository, $dispatcher);

            $coordinatorRepository = $this->em->getRepository(Program\Coordinator::class);
            $service = new ExecuteProgramTask($coordinatorRepository);

            $service->execute($this->firmId(), $this->personnelId(), $programId, $task, $registrantId);
            $dispatcher->finalize();
        });
        return $this->show($programId, $registrantId);
    }

    public function reject($programId, $registrantId)
    {
        $registrantRepository = $this->em->getRepository(Registrant2::class);
        $task = new RejectRegistrant($registrantRepository);

        $coordinatorRepository = $this->em->getRepository(Program\Coordinator::class);
        $service = new ExecuteProgramTask($coordinatorRepository);

        $service->execute($this->firmId(), $this->personnelId(), $programId, $task, $registrantId);

        return $this->show($programId, $registrantId);
    }

    public function show($programId, $registrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $registrant = $service->showById($this->firmId(), $programId, $registrantId);
        return $this->singleQueryResponse($this->arrayDataOfRegistrant($registrant));
    }

    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $concludedStatus = $this->filterBooleanOfQueryRequest("concludedStatus");
        $registrants = $service->showAll(
                $this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $concludedStatus);

        $result = [];
        $result['total'] = count($registrants);

        foreach ($registrants as $registrant) {
            $result['list'][] = $this->arrayDataOfRegistrant($registrant);
        }
        return $this->listQueryResponse($result);
    }

    public function arrayDataOfRegistrant(Registrant $registrant): array
    {
        return [
            "id" => $registrant->getId(),
            "registeredTime" => $registrant->getRegisteredTimeString(),
            "status" => $registrant->getStatus(),
            "user" => $registrant->getUserRegistrant() ? $this->arrayDataOfUser($registrant->getUserRegistrant()->getUser()) : null,
            "client" => $registrant->getClientRegistrant() ? $this->arrayDataOfClient($registrant->getClientRegistrant()->getClient()) : null,
            "team" => $registrant->getTeamRegistrant() ? $this->arrayDataOfTeam($registrant->getTeamRegistrant()->getTeam()) : null,
        ];
    }

    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            'id' => $participant->getId(),
            'enrolledTime' => $participant->getEnrolledTimeString(),
            'active' => $participant->isActive(),
            'note' => $participant->getNote(),
            'user' => $participant->getUserParticipant() ? $this->arrayDataOfUser($participant->getUserParticipant()->getUser()) : null,
            'client' => $participant->getClientParticipant() ? $this->arrayDataOfClient($participant->getClientParticipant()->getClient()) : null,
            'team' => $participant->getTeamParticipant() ? $this->arrayDataOfTeam($participant->getTeamParticipant()->getTeam()) : null,
        ];
    }
    protected function arrayDataOfUser(User $user): array
    {
        return [
            "id" => $user->getId(),
            "name" => $user->getFullName(),
        ];
    }
    protected function arrayDataOfClient(Client $client): array
    {
        return [
            "id" => $client->getId(),
            "name" => $client->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(Team $team): array
    {
        return [
            "id" => $team->getId(),
            "name" => $team->getName(),
        ];
    }

    public function buildViewService()
    {
        $registrantRepository = $this->em->getRepository(Registrant::class);
        return new ViewRegistrant($registrantRepository);
    }

}
