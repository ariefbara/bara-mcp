<?php

namespace App\Http\Controllers\Personnel\AsConsultantMeetingInitiator;

use Config\EventList;
use Firm\Application\Service\Personnel\ConsultantAttendee\ExecuteTaskAsMeetingInitiator;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Model\Firm\Program\Consultant\ConsultantAttendee;
use Firm\Domain\Task\MeetingInitiator\UpdateMeetingTask;
use Notification\Application\Listener\MeetingScheduleChangedListener;
use Notification\Application\Service\GenerateMeetingScheduleChangedNotification;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Query\Application\Service\Firm\ViewActivity;
use Query\Domain\Model\Firm\Program\Activity;
use Resources\Application\Event\Dispatcher;

class MeetingController extends AsMeetingInitiatorBaseController
{
    public function update($meetingId)
    {
        $consultantAttendeeRepository = $this->em->getRepository(ConsultantAttendee::class);
        $service = new ExecuteTaskAsMeetingInitiator($consultantAttendeeRepository);

        $task = new UpdateMeetingTask($this->getMeetingData(), $dispatcher = $this->getUpdateMeetingScheduleDispatcher());
        $service->execute($this->firmId(), $this->personnelId(), $meetingId, $task);
        
        $dispatcher->execute();
        
        $viewService = $this->buildViewService();
        $meeting = $viewService->showById($this->firmId(), $meetingId);
        
        $response = $this->singleQueryResponse($this->arrayDataOfMeeting($meeting));
        $this->sendAndCloseConnection($response, $this->buildSendImmediateMailJob());
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
        $meetingRepository = $this->em->getRepository(Meeting::class);
        $generateMeetingScheduleChangeNotification = new GenerateMeetingScheduleChangedNotification($meetingRepository);
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
        $activityRepository = $this->em->getRepository(Activity::class);
        return new ViewActivity($activityRepository);
    }

}
