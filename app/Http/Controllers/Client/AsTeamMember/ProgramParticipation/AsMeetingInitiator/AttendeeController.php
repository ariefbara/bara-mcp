<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation\AsMeetingInitiator;

use Firm\ {
    Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\CancelInvitation,
    Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\InviteConsultantToAttendMeeting,
    Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\InviteCoordinatorToAttendMeeting,
    Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\InviteManagerToAttendMeeting,
    Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\InviteParticipantToAttendMeeting,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Program\Coordinator,
    Domain\Model\Firm\Program\MeetingType\Meeting\Attendee,
    Domain\Model\Firm\Program\Participant,
    Domain\Model\Firm\Team\Member,
    Domain\Service\MeetingAttendeeBelongsToTeamFinder
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

    public function inviteManager($teamId, $meetingId)
    {
        $service = $this->buildInviteManagerService();
        $managerId = $this->stripTagsInputRequest("managerId");
        $service->execute($this->firmId(), $this->clientId(), $teamId, $meetingId, $managerId);

        return $this->commandOkResponse();
    }

    public function inviteCoordinator($teamId, $meetingId)
    {
        $service = $this->buildInviteCoordinatorService();
        $coordinatorId = $this->stripTagsInputRequest("coordinatorId");
        $service->execute($this->firmId(), $this->clientId(), $teamId, $meetingId, $coordinatorId);

        return $this->commandOkResponse();
    }

    public function inviteConsultant($teamId, $meetingId)
    {
        $service = $this->buildInviteConsultantService();
        $consultantId = $this->stripTagsInputRequest("consultantId");
        $service->execute($this->firmId(), $this->clientId(), $teamId, $meetingId, $consultantId);

        return $this->commandOkResponse();
    }

    public function inviteParticipant($teamId, $meetingId)
    {
        $service = $this->buildInviteParticipantService();
        $participantId = $this->stripTagsInputRequest("participantId");
        $service->execute($this->firmId(), $this->clientId(), $teamId, $meetingId, $participantId);

        return $this->commandOkResponse();
    }

    public function cancel($teamId, $meetingId, $attendeeId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $teamId, $meetingId, $attendeeId);

        return $this->commandOkResponse();
    }

    public function show($teamId, $meetingId, $attendeeId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $this->authorizeTeamIsMeetingInitiator($teamId, $meetingId);
        
        $service = $this->buildViewService();
        $attendee = $service->showById($this->firmId(), $meetingId, $attendeeId);
        return $this->singleQueryResponse($this->arrayDataOfInvitee($attendee));
    }

    public function showAll($teamId, $meetingId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $this->authorizeTeamIsMeetingInitiator($teamId, $meetingId);
        
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
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new InviteManagerToAttendMeeting(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $managerRepository);
    }

    protected function buildInviteCoordinatorService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new InviteCoordinatorToAttendMeeting(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $coordinatorRepository);
    }

    protected function buildInviteConsultantService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        return new InviteConsultantToAttendMeeting(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $consultantRepository);
    }

    protected function buildInviteParticipantService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        return new InviteParticipantToAttendMeeting(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $participantRepository);
    }

    protected function buildCancelService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        return new CancelInvitation(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $attendeeRepository);
    }

    protected function buildMeetingAttendeeBelongsToTeamFinder()
    {
        $meetingAttendeeRepository = $this->em->getRepository(Attendee::class);
        return new MeetingAttendeeBelongsToTeamFinder($meetingAttendeeRepository);
    }

}
