<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation\AsMeetingInitiator;

use Config\EventList;
use Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\CancelInvitation;
use Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\InviteConsultantToAttendMeeting;
use Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\InviteCoordinatorToAttendMeeting;
use Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\InviteManagerToAttendMeeting;
use Firm\Application\Service\Client\AsTeamMember\ProgramParticipant\AsMeetingAttendee\InviteParticipantToAttendMeeting;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Service\MeetingAttendeeBelongsToTeamFinder;
use Notification\Application\Listener\MeetingInvitationCancelledListener;
use Notification\Application\Listener\MeetingInvitationSentListener;
use Notification\Application\Service\GenerateMeetingInvitationCancelledNotification;
use Notification\Application\Service\GenerateMeetingInvitationSentNotification;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Query\Application\Service\Firm\Program\Activity\ViewInvitee;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Manager\ManagerInvitee;
use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Resources\Application\Event\Dispatcher;

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
            "willAttend" => $invitee->isWillAttend(),
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
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationSentListener($dispatcher);
        return new InviteManagerToAttendMeeting(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $managerRepository, $dispatcher);
    }

    protected function buildInviteCoordinatorService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationSentListener($dispatcher);
        return new InviteCoordinatorToAttendMeeting(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $coordinatorRepository, $dispatcher);
    }

    protected function buildInviteConsultantService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationSentListener($dispatcher);
        return new InviteConsultantToAttendMeeting(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $consultantRepository, $dispatcher);
    }
    protected function buildInviteParticipantService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationSentListener($dispatcher);
        return new InviteParticipantToAttendMeeting(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $participantRepository, $dispatcher);
    }
    protected function addMeetingInvitationSentListener(Dispatcher $dispatcher): void
    {
        $meetingAttendeeRepository = $this->em->getRepository(MeetingAttendee::class);
        $generateMeetingInvitationSentNotification = new GenerateMeetingInvitationSentNotification(
                $meetingAttendeeRepository);
        
        $listener = new MeetingInvitationSentListener(
                $generateMeetingInvitationSentNotification, $this->buildSendImmediateMail());
        $dispatcher->addListener(EventList::MEETING_INVITATION_SENT, $listener);
    }

    protected function buildCancelService()
    {
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationCancelledListener($dispatcher);
        return new CancelInvitation(
                $teamMemberRepository, $this->buildMeetingAttendeeBelongsToTeamFinder(), $attendeeRepository, $dispatcher);
    }
    protected function addMeetingInvitationCancelledListener(Dispatcher $dispatcher): void
    {
        $meetingAttendeeRepository = $this->em->getRepository(MeetingAttendee::class);
        $generateMeetingInvitationCancelledNotification = new GenerateMeetingInvitationCancelledNotification(
                $meetingAttendeeRepository);
        
        $listener = new MeetingInvitationCancelledListener(
                $generateMeetingInvitationCancelledNotification, $this->buildSendImmediateMail());
        $dispatcher->addListener(EventList::MEETING_INVITATION_CANCELLED, $listener);
    }

    protected function buildMeetingAttendeeBelongsToTeamFinder()
    {
        $meetingAttendeeRepository = $this->em->getRepository(Attendee::class);
        return new MeetingAttendeeBelongsToTeamFinder($meetingAttendeeRepository);
    }

}
