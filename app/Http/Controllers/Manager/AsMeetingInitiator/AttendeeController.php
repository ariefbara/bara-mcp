<?php

namespace App\Http\Controllers\Manager\AsMeetingInitiator;

use Firm\ {
    Application\Service\Manager\CancelInvitation,
    Application\Service\Manager\InviteConsultantToAttendMeeting,
    Application\Service\Manager\InviteCoordinatorToAttendMeeting,
    Application\Service\Manager\InviteManagerToAttendMeeting,
    Application\Service\Manager\InviteParticipantToAttendMeeting,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Program\Coordinator,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee,
    Domain\Model\Firm\Program\Participant
};
use Query\ {
    Application\Service\Firm\Program\Activity\ViewInvitee,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Manager\ManagerInvitee,
    Domain\Model\Firm\Program\Activity\Invitee,
    Domain\Model\Firm\Program\Consultant\ConsultantInvitee,
    Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee,
    Domain\Model\Firm\Program\Participant\ParticipantInvitee,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class AttendeeController extends AsMeetingInitiatorBaseController
{

    public function inviteManager($meetingId)
    {
        $service = $this->buildInviteManagerService();
        $managerId = $this->stripTagsInputRequest("managerId");
        $service->execute($this->firmId(), $this->managerId(), $meetingId, $managerId);
        
        return $this->commandOkResponse();
    }

    public function inviteCoordinator($meetingId)
    {
        $service = $this->buildInviteCoordinatorService();
        $coordinatorId = $this->stripTagsInputRequest("coordinatorId");
        $service->execute($this->firmId(), $this->managerId(), $meetingId, $coordinatorId);
        
        return $this->commandOkResponse();
    }

    public function inviteConsultant($meetingId)
    {
        $service = $this->buildInviteConsultantService();
        $consultantId = $this->stripTagsInputRequest("consultantId");
        $service->execute($this->firmId(), $this->managerId(), $meetingId, $consultantId);
        
        return $this->commandOkResponse();
    }

    public function inviteParticipant($meetingId)
    {
        $service = $this->buildInviteParticipantService();
        $participantId = $this->stripTagsInputRequest("participantId");
        $service->execute($this->firmId(), $this->managerId(), $meetingId, $participantId);
        
        return $this->commandOkResponse();
    }
    
    public function cancel($meetingId, $attendeeId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->managerId(), $meetingId, $attendeeId);
        
        return $this->commandOkResponse();
    }

    public function show($meetingId, $attendeeId)
    {
        $this->authorizeManagerIsMeetingInitiator($meetingId);
        $service = $this->buildViewService();
        $attendee = $service->showById($this->firmId(), $meetingId, $attendeeId);
        return $this->singleQueryResponse($this->arrayDataOfInvitee($attendee));
    }

    public function showAll($meetingId)
    {
        $this->authorizeManagerIsMeetingInitiator($meetingId);
        $service = $this->buildViewService();
        $attendees = $service->showAll($this->firmId(), $meetingId, $this->getPage(), $this->getPageSize(), false);
        
        $result = [];
        $result["total"] = count($attendees);
        foreach ($attendees as $attendee) {
            $result["list"][] = $this->arrayDataOfInvitee($attendee);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfInvitee(Invitee $invitee): array
    {
        return [
            "id" => $invitee->getId(),
            "willAttend" => $invitee->willAttend(),
            "attended" => $invitee->isAttended(),
            "manager" => $this->arrayDataOfManager($invitee->getManagerInvitee()),
            "coordinator" => $this->arrayDataOfCoordinator($invitee->getCoordinatorInvitee()),
            "consultant" => $this->arrayDataOfConsultant($invitee->getConsultantInvitee()),
            "participant" => $this->arrayDataOfParticipant($invitee->getParticipantInvitee()),
        ];
    }
    protected function arrayDataOfManager(?ManagerInvitee $managerInvitee): ?array
    {
        return empty($managerInvitee) ? null : [
            "id" => $managerInvitee->getManager()->getId(),
            "name" => $managerInvitee->getManager()->getName(),
        ];
    }
    protected function arrayDataOfCoordinator(?CoordinatorInvitee $coordinatorInvitee): ?array
    {
        return empty($coordinatorInvitee) ? null : [
            "id" => $coordinatorInvitee->getCoordinator()->getId(),
            "personnel" => [
                "id" => $coordinatorInvitee->getCoordinator()->getPersonnel()->getId(),
                "name" => $coordinatorInvitee->getCoordinator()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function arrayDataOfConsultant(?ConsultantInvitee $consultantInvitee): ?array
    {
        return empty($consultantInvitee) ? null : [
            "id" => $consultantInvitee->getConsultant()->getId(),
            "personnel" => [
                "id" => $consultantInvitee->getConsultant()->getPersonnel()->getId(),
                "name" => $consultantInvitee->getConsultant()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function arrayDataOfParticipant(?ParticipantInvitee $participantInvitee): ?array
    {
        return empty($participantInvitee) ? null : [
            "id" => $participantInvitee->getParticipant()->getId(),
            "user" => $this->arrayDataOfUser($participantInvitee->getParticipant()->getUserParticipant()),
            "client" => $this->arrayDataOfClient($participantInvitee->getParticipant()->getClientParticipant()),
            "team" => $this->arrayDataOfTeam($participantInvitee->getParticipant()->getTeamParticipant()),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }
    
    protected function buildViewService()
    {
        $inviteeRepository = $this->em->getRepository(Invitee::class);
        return new ViewInvitee($inviteeRepository);
    }
    
    protected function buildInviteManagerService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new InviteManagerToAttendMeeting($attendeeRepository, $managerRepository);
    }
    
    protected function buildInviteCoordinatorService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new InviteCoordinatorToAttendMeeting($attendeeRepository, $coordinatorRepository);
    }
    
    protected function buildInviteConsultantService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        return new InviteConsultantToAttendMeeting($attendeeRepository, $consultantRepository);
    }
    
    protected function buildInviteParticipantService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        return new InviteParticipantToAttendMeeting($attendeeRepository, $participantRepository);
    }
    
    protected function buildCancelService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        return new CancelInvitation($attendeeRepository);
    }

}
