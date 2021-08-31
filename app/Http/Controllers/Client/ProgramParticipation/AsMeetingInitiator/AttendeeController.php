<?php

namespace App\Http\Controllers\Client\ProgramParticipation\AsMeetingInitiator;

use Config\EventList;
use Firm\Application\Service\Client\ProgramParticipant\ExecuteTaskAsParticipantMeetinInitiator;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\ClientParticipant as ClientParticipant2;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Task\MeetingInitiator\CancelInvitationTask;
use Firm\Domain\Task\MeetingInitiator\InviteUserTask;
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

    public function inviteManager($programParticipationId, $initiatorId)
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $participantAttendeeRepository = $this->em->getRepository(ParticipantAttendee::class);
        $service = new ExecuteTaskAsParticipantMeetinInitiator($clientParticipantRepository, $participantAttendeeRepository);
        
        $managerRepository = $this->em->getRepository(Manager::class);
        $managerId = $this->stripTagsInputRequest("managerId");
        $dispatcher = $this->buildInvitationSentDispatcher();
        
        $task = new InviteUserTask($managerRepository, $managerId, $dispatcher);
        
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $initiatorId, $task);
        $dispatcher->execute();
        
        $this->sendAndCloseConnection();
        $this->sendImmediateMail();
    }

    public function inviteCoordinator($programParticipationId, $initiatorId)
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $participantAttendeeRepository = $this->em->getRepository(ParticipantAttendee::class);
        $service = new ExecuteTaskAsParticipantMeetinInitiator($clientParticipantRepository, $participantAttendeeRepository);
        
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $coordinatorId = $this->stripTagsInputRequest("coordinatorId");
        $dispatcher = $this->buildInvitationSentDispatcher();
        
        $task = new InviteUserTask($coordinatorRepository, $coordinatorId, $dispatcher);
        
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $initiatorId, $task);
        $dispatcher->execute();
        
        $this->sendAndCloseConnection();
        $this->sendImmediateMail();
    }

    public function inviteConsultant($programParticipationId, $initiatorId)
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $participantAttendeeRepository = $this->em->getRepository(ParticipantAttendee::class);
        $service = new ExecuteTaskAsParticipantMeetinInitiator($clientParticipantRepository, $participantAttendeeRepository);
        
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $consultantId = $this->stripTagsInputRequest("consultantId");
        $dispatcher = $this->buildInvitationSentDispatcher();
        
        $task = new InviteUserTask($consultantRepository, $consultantId, $dispatcher);
        
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $initiatorId, $task);
        $dispatcher->execute();
        
        $this->sendAndCloseConnection();
        $this->sendImmediateMail();
    }

    public function inviteParticipant($programParticipationId, $initiatorId)
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $participantAttendeeRepository = $this->em->getRepository(ParticipantAttendee::class);
        $service = new ExecuteTaskAsParticipantMeetinInitiator($clientParticipantRepository, $participantAttendeeRepository);
        
        $participantRepository = $this->em->getRepository(Participant::class);
        $participantId = $this->stripTagsInputRequest("participantId");
        $dispatcher = $this->buildInvitationSentDispatcher();
        
        $task = new InviteUserTask($participantRepository, $participantId, $dispatcher);
        
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $initiatorId, $task);
        $dispatcher->execute();
        
        $this->sendAndCloseConnection();
        $this->sendImmediateMail();
    }

    public function cancel($programParticipationId, $initiatorId, $attendeeId)
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        $participantAttendeeRepository = $this->em->getRepository(ParticipantAttendee::class);
        $service = new ExecuteTaskAsParticipantMeetinInitiator($clientParticipantRepository, $participantAttendeeRepository);
        
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $dispatcher = $this->buildInvitationCancelledDispatcher();
        $task = new CancelInvitationTask($attendeeRepository, $attendeeId, $dispatcher);
        
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $initiatorId, $task);
        $dispatcher->execute();
        
        $this->sendAndCloseConnection();
        $this->sendImmediateMail();
    }

    public function show($meetingId, $attendeeId)
    {
        $this->authorizeClientIsMeetingInitiator($meetingId);
        $service = $this->buildViewService();
        $attendee = $service->showById($this->firmId(), $meetingId, $attendeeId);
        return $this->singleQueryResponse($this->arrayDataOfInvitee($attendee));
    }

    public function showAll($meetingId)
    {
        $this->authorizeClientIsMeetingInitiator($meetingId);
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
    
    protected function buildInvitationSentDispatcher()
    {
        $meetingAttendeeRepository = $this->em->getRepository(MeetingAttendee::class);
        $generateMeetingInvitationSentNotification = new GenerateMeetingInvitationSentNotification($meetingAttendeeRepository);
        
        $listener = new MeetingInvitationSentListener($generateMeetingInvitationSentNotification);
        
        $dispatcher = new Dispatcher(false);
        $dispatcher->addListener(EventList::MEETING_INVITATION_SENT, $listener);
        
        return $dispatcher;
    }
    
    protected function buildInvitationCancelledDispatcher()
    {
        $meetingAttendeeRepository = $this->em->getRepository(MeetingAttendee::class);
        $generateMeetingInvitationCancelledNotification = new GenerateMeetingInvitationCancelledNotification($meetingAttendeeRepository);
        
        $listener = new MeetingInvitationCancelledListener($generateMeetingInvitationCancelledNotification);
        
        $dispatcher = new Dispatcher(false);
        $dispatcher->addListener(EventList::MEETING_INVITATION_CANCELLED, $listener);
        
        return $dispatcher;
    }

}
