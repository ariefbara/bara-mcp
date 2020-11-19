<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant;

use Firm\ {
    Application\Service\Personnel\ProgramConsultant\InitiateMeeting,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\Consultant,
    Domain\Model\Firm\Program\MeetingType\Meeting,
    Domain\Model\Firm\Program\MeetingType\MeetingData
};
use Query\ {
    Application\Service\Firm\Program\ViewActivity,
    Domain\Model\Firm\Program\Activity
};

class MeetingController extends AsProgramConsultantBaseController
{

    public function initiate($programId)
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
        
        $meetingId = $service->execute($this->firmId(), $this->personnelId(), $programId, $activityTypeId, $meetingData);
        
        $viewService = $this->buildViewService();
        $meeting = $viewService->showById($this->firmId(), $programId, $meetingId);
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
        $programConsultantRepository = $this->em->getRepository(Consultant::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        return new InitiateMeeting($meetingRepository, $programConsultantRepository, $activityTypeRepository);
    }

    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(Activity::class);
        return new ViewActivity($activityRepository);
    }

}
