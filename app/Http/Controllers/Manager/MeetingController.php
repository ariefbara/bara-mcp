<?php

namespace App\Http\Controllers\Manager;

use Firm\ {
    Application\Service\Manager\InitiateMeeting,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\MeetingType\Meeting,
    Domain\Model\Firm\Program\MeetingType\MeetingData
};
use Query\ {
    Application\Service\Firm\ViewActivity,
    Domain\Model\Firm\Program\Activity
};

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
        
        return new InitiateMeeting($meetingRepository, $managerRepository, $activityTypeRepository);
    }

    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(Activity::class);
        return new ViewActivity($activityRepository);
    }

}
