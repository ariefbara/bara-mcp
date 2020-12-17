<?php

namespace App\Http\Controllers\User\AsProgramParticipant;

use Config\EventList;
use Firm\Application\Service\User\ProgramParticipant\InitiateMeeting;
use Firm\Domain\Model\Firm\Program\ActivityType;
use Firm\Domain\Model\Firm\Program\MeetingType\Meeting;
use Firm\Domain\Model\Firm\Program\MeetingType\MeetingData;
use Firm\Domain\Model\Firm\Program\UserParticipant;
use Notification\Application\Listener\MeetingCreatedListener;
use Notification\Application\Service\GenerateMeetingCreatedNotification;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting as Meeting2;
use Query\Application\Service\Firm\Program\ViewActivity;
use Query\Domain\Model\Firm\Program\Activity;
use Resources\Application\Event\Dispatcher;

class MeetingController extends AsProgramParticipantBaseController
{

    public function initiate($firmId, $programId)
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
        
        $meetingId = $service->execute($this->userId(), $programId, $activityTypeId, $meetingData);
        
        $viewService = $this->buildViewService();
        $meeting = $viewService->showById($firmId, $programId, $meetingId);
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
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        $dispatcher = new Dispatcher();
        $this->addMeetingCreatedListenerToDispatcher($dispatcher);
        
        return new InitiateMeeting($meetingRepository, $userParticipantRepository, $activityTypeRepository, $dispatcher);
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
