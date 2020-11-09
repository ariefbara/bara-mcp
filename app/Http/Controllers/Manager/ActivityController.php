<?php

namespace App\Http\Controllers\Manager;

use ActivityCreator\ {
    Application\Service\Manager\InitiateActivity,
    Application\Service\Manager\UpdateActivity,
    Domain\DependencyModel\Firm\Manager,
    Domain\DependencyModel\Firm\Personnel\Consultant,
    Domain\DependencyModel\Firm\Personnel\Coordinator,
    Domain\DependencyModel\Firm\Program,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\DependencyModel\Firm\Program\Participant,
    Domain\Model\ManagerActivity as ManagerActivity2,
    Domain\service\ActivityDataProvider
};
use App\Http\Controllers\Manager\ManagerBaseController;
use Query\ {
    Application\Service\Firm\Manager\ViewManagerActivity,
    Domain\Model\Firm\Manager\ManagerActivity
};
use Resources\Application\Event\Dispatcher;

class ActivityController extends ManagerBaseController
{

    public function initiate()
    {
        $service = $this->buildInitiateService();
        $programId = $this->stripTagsInputRequest("programId");
        $activityTypeId = $this->stripTagsInputRequest("activityTypeId");

        $managerActivityId = $service->execute(
                $this->firmId(), $this->managerId(), $programId, $activityTypeId, $this->buildActivityDataProvider());
        
        $viewService = $this->buildViewService();
        $managerActivity = $viewService->showById($this->firmId(), $this->managerId(), $managerActivityId);
        return $this->commandCreatedResponse($this->arrayDataOfManagerActivity($managerActivity));
    }

    public function update($activityId)
    {
        $service = $this->buildUpdateService();
        $service->execute($this->firmId(), $this->managerId(), $activityId, $this->buildActivityDataProvider());
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
        
        foreach ($this->request->input("invitedManagerList") as $managerId) {
            $dataProvider->addManagerInvitation($managerId);
        }
        foreach ($this->request->input("invitedCoordinatorList") as $coordinatorId) {
            $dataProvider->addCoordinatorInvitation($coordinatorId);
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
        $this->authorizedUserIsFirmManager();
        $service = $this->buildViewService();
        $managerActivity = $service->showById($this->firmId(), $this->managerId(), $activityId);
        return $this->singleQueryResponse($this->arrayDataOfManagerActivity($managerActivity));
    }

    public function showAll()
    {
        $this->authorizedUserIsFirmManager();
        $service = $this->buildViewService();
        $managerActivities = $service->showAll($this->firmId(), $this->managerId(), $this->getPage(),
                $this->getPageSize());

        $result = [];
        $result["total"] = count($managerActivities);
        foreach ($managerActivities as $managerActivity) {
            $result["list"][] = $this->arrayDataOfManagerActivity($managerActivity);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfManagerActivity(ManagerActivity $managerActivity): array
    {
        return [
            "id" => $managerActivity->getId(),
            "activityType" => [
                "id" => $managerActivity->getActivityType()->getId(),
                "name" => $managerActivity->getActivityType()->getName(),
            ],
            "name" => $managerActivity->getName(),
            "description" => $managerActivity->getDescription(),
            "startTime" => $managerActivity->getStartTimeString(),
            "endTime" => $managerActivity->getEndTimeString(),
            "location" => $managerActivity->getLocation(),
            "note" => $managerActivity->getNote(),
            "cancelled" => $managerActivity->isCancelled(),
        ];
    }

    protected function buildViewService()
    {
        $activityRepository = $this->em->getRepository(ManagerActivity::class);
        return new ViewManagerActivity($activityRepository);
    }

    protected function buildInitiateService()
    {
        $managerActivityRepository = $this->em->getRepository(ManagerActivity2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        $programRepository = $this->em->getRepository(Program::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        $dispatcher = new Dispatcher();

        return new InitiateActivity(
                $managerActivityRepository, $managerRepository, $programRepository, $activityTypeRepository, $dispatcher);
    }

    protected function buildUpdateService()
    {
        $managerActivityRepository = $this->em->getRepository(ManagerActivity2::class);
        $dispatcher = new Dispatcher();
        return new UpdateActivity($managerActivityRepository, $dispatcher);
    }

}
