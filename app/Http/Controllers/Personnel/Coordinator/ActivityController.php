<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use ActivityCreator\{
    Application\Service\Coordinator\InitiateActivity,
    Application\Service\Coordinator\UpdateActivity,
    Domain\DependencyModel\Firm\Manager,
    Domain\DependencyModel\Firm\Personnel\Consultant,
    Domain\DependencyModel\Firm\Personnel\Coordinator,
    Domain\DependencyModel\Firm\Program,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\DependencyModel\Firm\Program\Participant,
    Domain\Model\CoordinatorActivity as CoordinatorActivity2,
    Domain\service\ActivityDataProvider
};
use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\{
    Application\Service\Firm\Personnel\ProgramCoordinator\ViewCoordinatorActivity,
    Domain\Model\Firm\Program\Coordinator\CoordinatorActivity
};
use Resources\Application\Event\Dispatcher;

class ActivityController extends PersonnelBaseController
{

    public function initiate($coordinatorId)
    {
        $service = $this->buildInitiateService();
        $activityTypeId = $this->stripTagsInputRequest("activityTypeId");

        $activityId = $service->execute(
                $this->firmId(), $this->personnelId(), $coordinatorId, $activityTypeId,
                $this->buildActivityDataProvider());

        $viewService = $this->buildViewService();
        $coordinatorActivity = $viewService->showById($this->firmId(), $this->personnelId(), $activityId);
        return $this->commandCreatedResponse($this->arrayDataOfCoordinatorActivity($coordinatorActivity));
    }

    public function update($activityId)
    {
        $service = $this->buildUpdateService();
        $service->execute($this->firmId(), $this->personnelId(), $activityId, $this->buildActivityDataProvider());
        return $this->show($activityId);
    }

    protected function buildActivityDataProvider()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        $name = $this->stripTagsInputRequest("name");
        $description = $this->stripTagsInputRequest("description");
        $startTime = $this->dateTimeImmutableOfInputRequest("startTime");
        $endTime = $this->dateTimeImmutableOfInputRequest("endTime");
        $location = $this->stripTagsInputRequest("location");
        $note = $this->stripTagsInputRequest("note");

        $dataProvider = new ActivityDataProvider(
                $managerRepository, $coordinatorRepository, $consultantRepository, $participantRepository, $name,
                $description, $startTime, $endTime, $location, $note);

        foreach ($this->request->input("invitedCoordinatorList") as $coordinatorId) {
            $dataProvider->addCoordinatorInvitation($coordinatorId);
        }
        foreach ($this->request->input("invitedManagerList") as $managerId) {
            $dataProvider->addManagerInvitation($managerId);
        }
        foreach ($this->request->input("invitedConsultantList") as $consultantId) {
            $dataProvider->addConsultantInvitation($consultantId);
        }
        foreach ($this->request->input("invitedParticipantList") as $participantId) {
            $dataProvider->addParticipantInvitation($participantId);
        }

        return $dataProvider;
    }

    public function show($activityId)
    {
        $service = $this->buildViewService();
        $coordinatorActivity = $service->showById($this->firmId(), $this->personnelId(), $activityId);
        return $this->singleQueryResponse($this->arrayDataOfCoordinatorActivity($coordinatorActivity));
    }

    public function showAll($coordinatorId)
    {
        $service = $this->buildViewService();
        $coordinatorActivities = $service->showAll(
                $this->firmId(), $this->personnelId(), $coordinatorId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($coordinatorActivities);
        foreach ($coordinatorActivities as $coordinatorActivity) {
            $result["list"][] = $this->arrayDataOfCoordinatorActivity($coordinatorActivity);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfCoordinatorActivity(CoordinatorActivity $coordinatorActivity): array
    {
        return [
            "id" => $coordinatorActivity->getId(),
            "activityType" => [
                "id" => $coordinatorActivity->getActivityType()->getId(),
                "name" => $coordinatorActivity->getActivityType()->getName(),
            ],
            "name" => $coordinatorActivity->getName(),
            "description" => $coordinatorActivity->getDescription(),
            "startTime" => $coordinatorActivity->getStartTimeString(),
            "endTime" => $coordinatorActivity->getEndTimeString(),
            "location" => $coordinatorActivity->getLocation(),
            "note" => $coordinatorActivity->getNote(),
            "cancelled" => $coordinatorActivity->isCancelled(),
        ];
    }

    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(CoordinatorActivity::class);
        return new ViewCoordinatorActivity($activityRepository);
    }

    protected function buildInitiateService()
    {
        $coordinatorActivityRepository = $this->em->getRepository(CoordinatorActivity2::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        $dispatcher = new Dispatcher();

        return new InitiateActivity(
                $coordinatorActivityRepository, $coordinatorRepository, $activityTypeRepository, $dispatcher);
    }

    protected function buildUpdateService()
    {
        $coordinatorActivityRepository = $this->em->getRepository(CoordinatorActivity2::class);
        $dispatcher = new Dispatcher();
        return new UpdateActivity($coordinatorActivityRepository, $dispatcher);
    }

}
