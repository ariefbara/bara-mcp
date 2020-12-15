<?php

namespace App\Http\Controllers\User\ProgramParticipation\AsMeetingInitiator;

use Config\EventList;
use Firm\Application\Service\User\MeetingAttendee\CancelInvitation;
use Firm\Application\Service\User\MeetingAttendee\InviteConsultantToAttendMeeting;
use Firm\Application\Service\User\MeetingAttendee\InviteCoordinatorToAttendMeeting;
use Firm\Application\Service\User\MeetingAttendee\InviteManagerToAttendMeeting;
use Firm\Application\Service\User\MeetingAttendee\InviteParticipantToAttendMeeting;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\Participant;
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

    public function inviteManager($meetingId)
    {
        $service = $this->buildInviteManagerService();
        $managerId = $this->stripTagsInputRequest("managerId");
        $service->execute($this->userId(), $meetingId, $managerId);
        
        return $this->commandOkResponse();
    }

    public function inviteCoordinator($meetingId)
    {
        $service = $this->buildInviteCoordinatorService();
        $coordinatorId = $this->stripTagsInputRequest("coordinatorId");
        $service->execute($this->userId(), $meetingId, $coordinatorId);
        
        return $this->commandOkResponse();
    }

    public function inviteConsultant($meetingId)
    {
        $service = $this->buildInviteConsultantService();
        $consultantId = $this->stripTagsInputRequest("consultantId");
        $service->execute($this->userId(), $meetingId, $consultantId);
        
        return $this->commandOkResponse();
    }

    public function inviteParticipant($meetingId)
    {
        $service = $this->buildInviteParticipantService();
        $participantId = $this->stripTagsInputRequest("participantId");
        $service->execute($this->userId(), $meetingId, $participantId);
        
        return $this->commandOkResponse();
    }
    
    public function cancel($meetingId, $attendeeId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->userId(), $meetingId, $attendeeId);
        
        return $this->commandOkResponse();
    }

    public function show($firmId, $meetingId, $attendeeId)
    {
        $this->authorizeUserIsMeetingInitiator($meetingId);
        $service = $this->buildViewService();
        $attendee = $service->showById($firmId, $meetingId, $attendeeId);
        return $this->singleQueryResponse($this->arrayDataOfInvitee($attendee));
    }

    public function showAll($firmId, $meetingId)
    {
        $this->authorizeUserIsMeetingInitiator($meetingId);
        $service = $this->buildViewService();
        $attendees = $service->showAll($firmId, $meetingId, $this->getPage(), $this->getPageSize(), false);
        
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
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationSentListener($dispatcher);
        
        return new InviteManagerToAttendMeeting($attendeeRepository, $managerRepository, $dispatcher);
    }
    
    protected function buildInviteCoordinatorService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationSentListener($dispatcher);
        
        return new InviteCoordinatorToAttendMeeting($attendeeRepository, $coordinatorRepository, $dispatcher);
    }
    
    protected function buildInviteConsultantService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationSentListener($dispatcher);
        
        return new InviteConsultantToAttendMeeting($attendeeRepository, $consultantRepository, $dispatcher);
    }
    
    protected function buildInviteParticipantService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationSentListener($dispatcher);
        
        return new InviteParticipantToAttendMeeting($attendeeRepository, $participantRepository, $dispatcher);
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
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingInvitationCancelledListener($dispatcher);
        
        return new CancelInvitation($attendeeRepository, $dispatcher);
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

}
