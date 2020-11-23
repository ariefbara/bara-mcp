<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant;

use Firm\{
    Application\Service\Client\AsTeamMember\AsProgramParticipant\InitiateMeeting,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\MeetingType\Meeting,
    Domain\Model\Firm\Program\MeetingType\MeetingData,
    Domain\Model\Firm\Program\TeamParticipant,
    Domain\Model\Firm\Team\Member
};
use Query\{
    Application\Service\Firm\Program\ViewActivity,
    Domain\Model\Firm\Program\Activity
};

class MeetingController extends AsProgramParticipantBaseController
{

    public function initiate($teamId, $programId)
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

        $meetingId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $programId, $activityTypeId, $meetingData);

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
        $teamMemberRepository = $this->em->getRepository(Member::class);
        $teamParticipantRepository = $this->em->getRepository(TeamParticipant::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);

        return new InitiateMeeting(
                $meetingRepository, $teamMemberRepository, $teamParticipantRepository, $activityTypeRepository);
    }

    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(Activity::class);
        return new ViewActivity($activityRepository);
    }

}
