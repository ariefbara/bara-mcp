<?php

namespace App\Http\Controllers\Client\ProgramParticipation\AsMeetingInitiator;

use Config\EventList;
use Firm\Application\Service\Client\ProgramParticipant\UpdateMeeting;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting\Attendee;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
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
        $service = $this->buildUpdateService();

        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $endTime = $this->dateTimeImmutableOfInputRequest("endTime");
        $location = $this->stripTagsInputRequest("location");
        $note = $this->stripTagsInputRequest("note");

        $meetingData = new MeetingData($name, $description, $startTime, $endTime, $location, $note);
        $service->execute($this->firmId(), $this->clientId(), $meetingId, $meetingData);

        $viewService = $this->buildViewService();
        $meeting = $viewService->showById($this->firmId(), $meetingId);
        return $this->singleQueryResponse($this->arrayDataOfMeeting($meeting));
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

    protected function buildUpdateService()
    {
        $attendeeRepository = $this->em->getRepository(Attendee::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingScheduleChangedListener($dispatcher);
        return new UpdateMeeting($attendeeRepository, $dispatcher);
    }
    protected function addMeetingScheduleChangedListener(Dispatcher $dispatcher): void
    {
        $meetingRepository = $this->em->getRepository(Meeting::class);
        $generateMeetingScheduleChangeNotification = new GenerateMeetingScheduleChangedNotification($meetingRepository);
        $listener = new MeetingScheduleChangedListener(
                $generateMeetingScheduleChangeNotification, $this->buildSendImmediateMail());
        $dispatcher->addListener(EventList::MEETING_SCHEDULE_CHANGED, $listener);
    }

    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(Activity::class);
        return new ViewActivity($activityRepository);
    }

}
