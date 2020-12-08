<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant;

use Query\{
    Application\Service\Firm\Program\ViewActivityType,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\ActivityType\ActivityParticipant
};

class ActivityTypeController extends AsProgramParticipantBaseController
{

    public function showAll($teamId, $programId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $this->authorizedTeamIsActiveParticipantOfProgram($teamId, $programId);

        $service = $this->buildViewService();
        $activityTypes = $service->showAll(
                $programId, $this->getPage(), $this->getPageSize(), $enabledOnly = true,
                $userRoleAllowedToInitiate = "participant");
        return $this->commonIdNameListQueryResponse($activityTypes);
    }

    public function show($teamId, $programId, $activityTypeId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $this->authorizedTeamIsActiveParticipantOfProgram($teamId, $programId);

        $service = $this->buildViewService();
        $activityType = $service->showById($programId, $activityTypeId);
        return $this->singleQueryResponse($this->arrayDataOfActivityType($activityType));
    }

    protected function arrayDataOfActivityType(ActivityType $activityType): array
    {
        $participants = [];
        foreach ($activityType->iterateParticipants() as $activityParticipant) {
            $participants[] = $this->arrayDataOfActivityParticipant($activityParticipant);
        }

        return [
            "id" => $activityType->getId(),
            "name" => $activityType->getName(),
            "description" => $activityType->getDescription(),
            "participants" => $participants,
        ];
    }

    protected function arrayDataOfActivityParticipant(ActivityParticipant $activityParticipant): array
    {
        return [
            "id" => $activityParticipant->getId(),
            "participantType" => $activityParticipant->getParticipantType(),
            "canInitiate" => $activityParticipant->canInitiate(),
            "canAttend" => $activityParticipant->canAttend(),
            "feedbackForm" => $this->arrayDataOfFeedbackForm($activityParticipant->getReportForm()),
        ];
    }

    protected function arrayDataOfFeedbackForm(?FeedbackForm $feedbackForm): ?array
    {
        return empty($feedbackForm) ? null : [
            "id" => $feedbackForm->getId(),
            "name" => $feedbackForm->getName(),
            "description" => $feedbackForm->getDescription(),
        ];
    }

    protected function buildViewService()
    {
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        return new ViewActivityType($activityTypeRepository);
    }

}
