<?php

namespace App\Http\Controllers\Manager;

use Config\EventList;
use Firm\Application\Service\Manager\InitiateMeeting;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Notification\Application\Listener\MeetingCreatedListener;
use Notification\Application\Service\GenerateMeetingCreatedNotification;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting as Meeting2;
use Query\Application\Service\Firm\ViewActivity;
use Query\Domain\Model\Firm\Program\Activity;
use Resources\Application\Event\Dispatcher;

class MeetingController extends ManagerBaseController
{

    public function initiate()
    {
        $service = $this->buildInitiateService();
        $activityTypeId = $this->stripTagsInputRequest("meetingTypeId");
        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $endTime = $this->dateTimeImmutableOfInputRequest("endTime");
        $location = $this->stripTagsInputRequest("location");
        $note = $this->stripTagsInputRequest("note");
        
        $meetingData = new MeetingData($name, $description, $startTime, $endTime, $location, $note);
        
        $meetingId = $service->execute($this->firmId(), $this->managerId(), $activityTypeId, $meetingData);
        
        $viewService = $this->buildViewService();
        $meeting = $viewService->showById($this->firmId(), $meetingId);
        return $this->commandCreatedResponse($this->arrayDataOfMeeting($meeting));
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

    protected function buildInitiateService()
    {
        $meetingRepository = $this->em->getRepository(Meeting::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingCreatedListenerToDispatcher($dispatcher);
        
        return new InitiateMeeting($meetingRepository, $managerRepository, $activityTypeRepository, $dispatcher);
    }
    protected function addMeetingCreatedListenerToDispatcher(Dispatcher $dispatcher): void
    {
        $meetingRepository = $this->em->getRepository(Meeting2::class);
        $generateMeetingCreaterNotification = new GenerateMeetingCreatedNotification($meetingRepository);
        $sendImmediateMail = $this->buildSendImmediateMail();
        $listener =  new MeetingCreatedListener($generateMeetingCreaterNotification, $sendImmediateMail);
        $dispatcher->addListener(EventList::MEETING_CREATED, $listener);
    }

    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(Activity::class);
        return new ViewActivity($activityRepository);
    }

}
