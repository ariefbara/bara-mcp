<?php

namespace App\Http\Controllers\Client\ProgramParticipation\AsMeetingInitiator;

use Config\EventList;
use Firm\Application\Service\Client\ProgramParticipant\ExecuteTaskAsParticipantMeetinInitiator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\ClientParticipant;
use Firm\Domain\Model\Firm\Program\Participant\ParticipantAttendee;
use Firm\Domain\Task\MeetingInitiator\UpdateMeetingTask;
use Notification\Application\Listener\MeetingScheduleChangedListener;
use Notification\Application\Service\GenerateMeetingScheduleChangedNotification;
use Query\Application\Service\Firm\Client\ProgramParticipation\ViewInvitationForClientParticipant;
use Query\Domain\Model\Firm\Program\Activity;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Resources\Application\Event\Dispatcher;

class MeetingController extends AsMeetingInitiatorBaseController
{

    public function update($programParticipationId, $initiatorId)
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $participantAttendeeRepository = $this->em->getRepository(ParticipantAttendee::class);
        $service = new ExecuteTaskAsParticipantMeetinInitiator($clientParticipantRepository, $participantAttendeeRepository);
        
        $task = new UpdateMeetingTask($this->getMeetingData(), $dispatcher = $this->getUpdateMeetingScheduleDispatcher());
        
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $initiatorId, $task);
        $dispatcher->execute();
        
        $viewService = $this->buildViewService();
        $meeting = $viewService->showById($this->firmId(), $this->clientId(), $initiatorId)->getActivity();
        
        $this->sendAndCloseConnection($this->arrayDataOfMeeting($meeting));
        $this->sendImmediateMail();
    }
    
    protected function getMeetingData()
    {
        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $endTime = $this->dateTimeImmutableOfInputRequest("endTime");
        $location = $this->stripTagsInputRequest("location");
        $note = $this->stripTagsInputRequest("note");

        return new MeetingData($name, $description, $startTime, $endTime, $location, $note);
    }
    
    protected function getUpdateMeetingScheduleDispatcher()
    {
        $meetingRepository = $this->em->getRepository(\Notification\Domain\Model\Firm\Program\MeetingType\Meeting::class);
        $generateMeetingScheduleChangeNotification = new GenerateMeetingScheduleChangedNotification($meetingRepository);
        $sendImmediateMail = $this->buildSendImmediateMail();
        $listener = new MeetingScheduleChangedListener($generateMeetingScheduleChangeNotification);
        
        $dispatcher = new Dispatcher(false);
        $dispatcher->addListener(EventList::MEETING_SCHEDULE_CHANGED, $listener);
        return $dispatcher;
    }

    protected function arrayDataOfMeeting(Activity $meeting): array
    {
        return [
            "id" => $meeting->getId(),
            "name" => $meeting->getName(),
            "description" => $meeting->getDescription(),
            "startTime" => $meeting->getStartTimeString(),
            "endTime" => $meeting->getEndTimeString(),
            "location" => $meeting->getLocation(),
            "note" => $meeting->getNote(),
            "cancelled" => $meeting->isCancelled(),
            "createdTime" => $meeting->getCreatedTimeString(),
        ];
    }

    protected function buildViewService()
    {
        $participantInvitationRepository = $this->em->getRepository(ParticipantInvitee::class);
        return new ViewInvitationForClientParticipant($participantInvitationRepository);
    }

}
