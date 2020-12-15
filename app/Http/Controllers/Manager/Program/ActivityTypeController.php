<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\{
    Application\Service\Manager\CreateActivityType,
    Application\Service\Manager\DisableActivityType,
    Application\Service\Manager\EnableActivityType,
    Application\Service\Manager\UpdateActivityType,
    Domain\Model\Firm\FeedbackForm as FeedbackForm2,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\ActivityType as ActivityType2,
    Domain\Service\ActivityTypeDataProvider
};
use Query\{
    Application\Service\Firm\Program\ViewActivityType,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\ActivityType\ActivityParticipant
};

class ActivityTypeController extends ManagerBaseController
{

    public function create($programId)
    {
        $service = $this->buildCreateService();
        $activityTypeId = $service->execute(
                $this->firmId(), $this->managerId(), $programId, $this->getActivityTypeDataProvider());

        $viewService = $this->buildViewService();
        $activityType = $viewService->showById($programId, $activityTypeId);
        return $this->commandCreatedResponse($this->arrayDataOfActivityType($activityType));
    }

    public function update($programId, $activityTypeId)
    {
        $service = $this->buildUpdateService();
        $service->execute($this->firmId(), $this->managerId(), $activityTypeId, $this->getActivityTypeDataProvider());

        return $this->show($programId, $activityTypeId);
    }

    public function disable($programId, $activityTypeId)
    {
        $service = $this->buildDisableService();
        $service->execute($this->firmId(), $this->managerId(), $activityTypeId);
        return $this->show($programId, $activityTypeId);
    }

    public function enable($programId, $activityTypeId)
    {
        $this->buildEnableService()->execute($this->firmId(), $this->managerId(), $activityTypeId);
        return $this->show($programId, $activityTypeId);
    }

    protected function getActivityTypeDataProvider()
    {
        $feedbackFormRepository = $this->em->getRepository(FeedbackForm2::class);
        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $activityTypeDataProvider = new ActivityTypeDataProvider($feedbackFormRepository, $name, $description);

        foreach ($this->request->input("participants") as $participant) {
            $participantType = $this->stripTagsVariable($participant['participantType']);
            $canInitiate = $this->filterBooleanOfVariable($participant['canInitiate']);
            $canAttend = $this->filterBooleanOfVariable($participant['canAttend']);
            $feedbackFormId = $this->stripTagsVariable($participant["feedbackFormId"]);
            $activityTypeDataProvider->addActivityParticipantData($participantType, $canInitiate, $canAttend,
                    $feedbackFormId);
        }

        return $activityTypeDataProvider;
    }

    public function showAll($programId)
    {
        $this->authorizedUserIsFirmManager();
        $service = $this->buildViewService();
        $activityTypes = $service->showAll(
                $programId, $this->getPage(), $this->getPageSize(), $enabledOnly = false,
                $userRoleAllowedToInitiate = null);

        $result = [];
        $result["total"] = count($activityTypes);
        foreach ($activityTypes as $activityType) {
            $result["list"][] = [
                "id" => $activityType->getId(),
                "name" => $activityType->getName(),
                "disabled" => $activityType->isDisabled(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function showAllInitableActivityType($programId)
    {
        $this->authorizedUserIsFirmManager();
        $service = $this->buildViewService();
        $activityTypes = $service->showAll(
                $programId, $this->getPage(), $this->getPageSize(), $enabledOnly = true,
                $userRoleAllowedToInitiate = "manager");

        $result = [];
        $result["total"] = count($activityTypes);
        foreach ($activityTypes as $activityType) {
            $result["list"][] = [
                "id" => $activityType->getId(),
                "name" => $activityType->getName(),
                "disabled" => $activityType->isDisabled(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $activityTypeId)
    {
        $this->authorizedUserIsFirmManager();
        $service = $this->buildViewService();
        $activityType = $service->showById($programId, $activityTypeId);

        return $this->singleQueryResponse($this->arrayDataOfActivityType($activityType));
    }

    protected function arrayDataOfActivityType(ActivityType $activityType): array
    {
        $participants = [];
        foreach ($activityType->iterateParticipants($enableOnly = false) as $activityParticipant) {
            $participants[] = $this->arrayDataOfActivityParticipant($activityParticipant);
        }

        return [
            "id" => $activityType->getId(),
            "name" => $activityType->getName(),
            "description" => $activityType->getDescription(),
            "disabled" => $activityType->isDisabled(),
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
            "disabled" => $activityParticipant->isDisabled(),
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

    protected function buildCreateService()
    {
        $activityTypeRepository = $this->em->getRepository(ActivityType2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        $programRepository = $this->em->getRepository(Program::class);
        return new CreateActivityType($activityTypeRepository, $managerRepository, $programRepository);
    }

    protected function buildUpdateService()
    {
        $activityTypeRepository = $this->em->getRepository(ActivityType2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new UpdateActivityType($activityTypeRepository, $managerRepository);
    }

    protected function buildDisableService()
    {
        $activityTypeRepository = $this->em->getRepository(ActivityType2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new DisableActivityType($activityTypeRepository, $managerRepository);
    }

    protected function buildEnableService()
    {
        $activityTypeRepository = $this->em->getRepository(ActivityType2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new EnableActivityType($activityTypeRepository, $managerRepository);
    }

}
