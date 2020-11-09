<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use ActivityCreator\{
    Application\Service\UserParticipant\InitiateActivity,
    Application\Service\UserParticipant\UpdateActivity,
    Domain\DependencyModel\Firm\Manager,
    Domain\DependencyModel\Firm\Personnel\Consultant,
    Domain\DependencyModel\Firm\Personnel\Coordinator,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\DependencyModel\Firm\Program\Participant,
    Domain\DependencyModel\User\ProgramParticipation,
    Domain\Model\ParticipantActivity as ParticipantActivity2,
    Domain\service\ActivityDataProvider
};
use App\Http\Controllers\User\UserBaseController;
use Query\{
    Application\Service\User\ProgramParticipation\ViewParticipantActivity,
    Domain\Model\Firm\Program\Participant\ParticipantActivity
};
use Resources\Application\Event\Dispatcher;

class ActivityController extends UserBaseController
{

    public function initiate($programParticipationId)
    {
        $service = $this->buildInitiateService();
        $activityTypeId = $this->stripTagsInputRequest("activityTypeId");

        $activityId = $service->execute(
                $this->userId(), $programParticipationId, $activityTypeId, $this->buildActivityDataProvider());

        $viewService = $this->buildViewService();
        $participantActivity = $viewService->showById($this->userId(), $activityId);
        return $this->commandCreatedResponse($this->arrayDataOfParticipantActivity($participantActivity));
    }

    public function update($programParticipationId, $activityId)
    {
        $service = $this->buildUpdateService();
        $service->execute($this->userId(), $activityId, $this->buildActivityDataProvider());
        return $this->show($programParticipationId, $activityId);
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
        foreach ($this->request->input("invitedCoordinatorList") as $consultantId) {
            $dataProvider->addCoordinatorInvitation($consultantId);
        }
        foreach ($this->request->input("invitedConsultantList") as $consultantId) {
            $dataProvider->addConsultantInvitation($consultantId);
        }
        foreach ($this->request->input("invitedParticipantList") as $participantId) {
            $dataProvider->addParticipantInvitation($participantId);
        }

        return $dataProvider;
    }

    public function show($programParticipationId, $activityId)
    {
        $service = $this->buildViewService();
        $participantActivity = $service->showById($this->userId(), $activityId);
        return $this->singleQueryResponse($this->arrayDataOfParticipantActivity($participantActivity));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $participantActivities = $service->showAll(
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($participantActivities);
        foreach ($participantActivities as $participantActivity) {
            $result["list"][] = $this->arrayDataOfParticipantActivity($participantActivity);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfParticipantActivity(ParticipantActivity $participantActivity): array
    {
        return [
            "id" => $participantActivity->getId(),
            "activityType" => [
                "id" => $participantActivity->getActivityType()->getId(),
                "name" => $participantActivity->getActivityType()->getName(),
            ],
            "name" => $participantActivity->getName(),
            "description" => $participantActivity->getDescription(),
            "startTime" => $participantActivity->getStartTimeString(),
            "endTime" => $participantActivity->getEndTimeString(),
            "location" => $participantActivity->getLocation(),
            "note" => $participantActivity->getNote(),
            "cancelled" => $participantActivity->isCancelled(),
        ];
    }

    protected function buildViewService()
    {
        $participantActivityRepository = $this->em->getRepository(ParticipantActivity::class);
        return new ViewParticipantActivity($participantActivityRepository);
    }

    protected function buildInitiateService()
    {
        $participantActivityRepository = $this->em->getRepository(ParticipantActivity2::class);
        $userParticipantRepository = $this->em->getRepository(ProgramParticipation::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        $dispatcher = new Dispatcher();

        return new InitiateActivity($participantActivityRepository, $userParticipantRepository, $activityTypeRepository,
                $dispatcher);
    }

    protected function buildUpdateService()
    {
        $participantActivityRepository = $this->em->getRepository(ParticipantActivity2::class);
        $dispatcher = new Dispatcher();
        return new UpdateActivity($participantActivityRepository, $dispatcher);
    }

}
