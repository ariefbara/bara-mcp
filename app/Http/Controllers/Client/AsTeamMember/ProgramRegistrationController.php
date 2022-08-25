<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Config\EventList;
use Firm\Application\Listener\AcceptProgramApplicationFromTeam;
use Firm\Application\Listener\GenerateTeamRegistrantInvoice;
use Firm\Domain\Model\Firm\Program as Program2;
use Firm\Domain\Model\Firm\Team;
use Firm\Domain\Model\Firm\Team\TeamRegistrant;
use Participant\Application\Service\Firm\Client\TeamMembership\CancelTeamProgramRegistration;
use Participant\Application\Service\Firm\Client\TeamMembership\RegisterTeamToProgram;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\Model\TeamProgramRegistration;
use Query\Application\Service\Firm\Team\ViewTeamProgramParticipation;
use Query\Application\Service\Firm\Team\ViewTeamProgramRegistration;
use Query\Domain\Model\Firm\Program as Program3;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\Firm\Team\TeamProgramRegistration as TeamProgramRegistration2;
use Resources\Application\Event\AdvanceDispatcher;
use Resources\Application\Listener\SpyEntityCreation;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;
use SharedContext\Infrastructure\Xendit\XenditPaymentGateway;
use Team\Application\Service\TeamMember\ExecuteTeamTask;
use Team\Domain\Model\Team\Member;
use Team\Domain\Task\ApplyToProgram;

class ProgramRegistrationController extends AsTeamMemberBaseController
{

    public function register($teamId)
    {
        $dispatcher = new AdvanceDispatcher();
        
        $programRepository = $this->em->getRepository(Program2::class);
        $teamRepository = $this->em->getRepository(Team::class);
        $acceptProgramApplicationFromTeam = new AcceptProgramApplicationFromTeam(
                $programRepository, $teamRepository, $dispatcher);
        $dispatcher->addImmediateListener(EventList::TEAM_APPLIED_TO_PROGRAM, $acceptProgramApplicationFromTeam);
        
        $spyRegistrantCreation = new SpyEntityCreation();
        $dispatcher->addPostponedListener(EventList::PROGRAM_REGISTRATION_RECEIVED, $spyRegistrantCreation);
        $dispatcher->addPostponedListener(EventList::SETTLEMENT_REQUIRED, $spyRegistrantCreation);
        
        $teamRegistrantRepository = $this->em->getRepository(TeamRegistrant::class);
        $paymentGateway = new XenditPaymentGateway();
        $generateTeamRegistrantInvoice = new GenerateTeamRegistrantInvoice($teamRegistrantRepository, $paymentGateway);
        $dispatcher->addAsynchronousListener(EventList::SETTLEMENT_REQUIRED, $generateTeamRegistrantInvoice);
        
        $spyParticipantCreation = new SpyEntityCreation();
        $dispatcher->addPostponedListener(EventList::PROGRAM_PARTICIPATION_ACCEPTED, $spyParticipantCreation);
        
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $service = new ExecuteTeamTask($teamMemberRepository);
        
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $transactionalSession->executeAtomically(function () use ($service, $dispatcher, $teamId) {
            $task = new ApplyToProgram($dispatcher);
            $programId = $this->request->input('programId');
            $service->execute($this->firmId(), $this->clientId(), $teamId, $task, $programId);
            
            $dispatcher->finalize();
        });
        $dispatcher->finalizeAsynchronous();
        
        if ($registrantId = $spyRegistrantCreation->getEntityId()) {
            $teamProgramRegistration = $this->buildViewService()->showById($this->firmId(), $teamId, $registrantId);
            $result = $this->arrayDataOfTeamProgramRegistration($teamProgramRegistration);
        } elseif ($participantId = $spyParticipantCreation->getEntityId()) {
            $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
            $teamParticipant = (new ViewTeamProgramParticipation($teamProgramParticipationRepository))
                    ->showById($teamId, $participantId);
            $result = $this->arrayDataOfTeamParticipant($teamParticipant);
        }
        return $this->commandCreatedResponse($result);
    }

//    public function cancel($teamId, $teamProgramRegistrationId)
//    {
//        $service = $this->buildCancelService();
//        $service->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramRegistrationId);
//        return $this->commandOkResponse();
//    }

    public function show($teamId, $teamProgramRegistrationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $service = $this->buildViewService();
        $teamProgramRegistration = $service->showById($this->firmId(), $teamId, $teamProgramRegistrationId);
        return $this->singleQueryResponse($this->arrayDataOfTeamProgramRegistration($teamProgramRegistration));
    }

    public function showAll($teamId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $service = $this->buildViewService();
        $concludedStatus = $this->filterBooleanOfQueryRequest("concludedStatus");
        $teamProgramRegistrations = $service->showAll(
                $this->firmId(), $teamId, $this->getPage(), $this->getPageSize(), $concludedStatus);

        $result = [];
        $result["total"] = count($teamProgramRegistrations);
        foreach ($teamProgramRegistrations as $teamProgramRegistration) {
            $result["list"][] = $this->arrayDataOfTeamProgramRegistration($teamProgramRegistration);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfTeamProgramRegistration(TeamProgramRegistration2 $teamProgramRegistration): array
    {
        return [
            "id" => $teamProgramRegistration->getId(),
            "registeredTime" => $teamProgramRegistration->getRegisteredTimeString(),
            'status' => $teamProgramRegistration->getStatus(),
            'programSnapshot' => [
                "price" => $teamProgramRegistration->getProgramSnapshot()->getPrice(),
                "autoAccept" => $teamProgramRegistration->getProgramSnapshot()->isAutoAccept(),
            ],
            "program" => $this->arrayDataOfProgram($teamProgramRegistration->getProgram()),
            'invoice' => $teamProgramRegistration->getRegistrantInvoice() ? 
                $this->arrayDataOfRegistrantInvoice($teamProgramRegistration->getRegistrantInvoice()) : null,
        ];
    }
    protected function arrayDataOfTeamParticipant(TeamProgramParticipation $teamParticipant): array
    {
        return [
            "id" => $teamParticipant->getId(),
            "enrolledTime" => $teamParticipant->getEnrolledTimeString(),
            "note" => $teamParticipant->getNote(),
            "active" => $teamParticipant->isActive(),
            "program" => $this->arrayDataOfProgram($teamParticipant->getProgram()),
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
    protected function arrayDataOfRegistrantInvoice(Program3\Registrant\RegistrantInvoice $registrantInvoice): array
    {
        return [
            'issuedTime' => $registrantInvoice->getIssuedTimeString(),
            'expiredTime' => $registrantInvoice->getExpiredTimeString(),
            'paymentLink' => $registrantInvoice->getPaymentLink(),
            'settled' => $registrantInvoice->isSettled(),
        ];
    }

    protected function buildViewService()
    {
        $teamProgramRegistrationRepository = $this->em->getRepository(TeamProgramRegistration2::class);
        return new ViewTeamProgramRegistration($teamProgramRegistrationRepository);
    }

    protected function buildRegisterService()
    {
        $teamProgramRegistrationRepository = $this->em->getRepository(TeamProgramRegistration::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $programRepository = $this->em->getRepository(Program::class);

        return new RegisterTeamToProgram($teamProgramRegistrationRepository, $teamMembershipRepository,
                $programRepository);
    }

    protected function buildCancelService()
    {
        $teamProgramRegistrationRepository = $this->em->getRepository(TeamProgramRegistration::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        return new CancelTeamProgramRegistration($teamProgramRegistrationRepository, $teamMembershipRepository);
    }

}
