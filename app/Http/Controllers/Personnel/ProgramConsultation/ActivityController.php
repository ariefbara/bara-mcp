<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use ActivityCreator\ {
    Application\Service\Consultant\InitiateActivity,
    Application\Service\Consultant\UpdateActivity,
    Domain\DependencyModel\Firm\Manager,
    Domain\DependencyModel\Firm\Personnel\Consultant,
    Domain\DependencyModel\Firm\Personnel\Coordinator,
    Domain\DependencyModel\Firm\Program\ActivityType,
    Domain\DependencyModel\Firm\Program\Participant,
    Domain\Model\ConsultantActivity as ConsultantActivity2,
    Domain\service\ActivityDataProvider
};
use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ViewConsultantActivity,
    Domain\Model\Firm\Program\Consultant\ConsultantActivity
};
use Resources\Application\Event\Dispatcher;

class ActivityController extends PersonnelBaseController
{

    public function initiate($programConsultationId)
    {
        $service = $this->buildInitiateService();
        $activityTypeId = $this->stripTagsInputRequest("activityTypeId");

        $consultantActivityId = $service->execute(
                $this->firmId(), $this->personnelId(), $programConsultationId, $activityTypeId, $this->buildActivityDataProvider());

        $viewService = $this->buildViewService();
        $consultantActivity = $viewService->showById($this->firmId(), $this->personnelId(), $consultantActivityId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultantActivity($consultantActivity));
    }

    public function update($programConsultationId, $activityId)
    {
        $service = $this->buildUpdateService();
        $service->execute($this->firmId(), $this->personnelId(), $activityId, $this->buildActivityDataProvider());
        return $this->show($programConsultationId, $activityId);
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

    public function show($programConsultationId, $activityId)
    {
        $service = $this->buildViewService();
        $consultantActivity = $service->showById($this->firmId(), $this->personnelId(), $activityId);
        return $this->singleQueryResponse($this->arrayDataOfConsultantActivity($consultantActivity));
    }

    public function showAll($programConsultationId)
    {
        $service = $this->buildViewService();
        $consultantActivities = $service->showAll(
                $this->firmId(), $this->personnelId(), $programConsultationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($consultantActivities);
        foreach ($consultantActivities as $consultantActivity) {
            $result["list"][] = $this->arrayDataOfConsultantActivity($consultantActivity);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultantActivity(ConsultantActivity $consultantActivity): array
    {
        return [
            "id" => $consultantActivity->getId(),
            "activityType" => [
                "id" => $consultantActivity->getActivityType()->getId(),
                "name" => $consultantActivity->getActivityType()->getName(),
            ],
            "name" => $consultantActivity->getName(),
            "description" => $consultantActivity->getDescription(),
            "startTime" => $consultantActivity->getStartTimeString(),
            "endTime" => $consultantActivity->getEndTimeString(),
            "location" => $consultantActivity->getLocation(),
            "note" => $consultantActivity->getNote(),
            "cancelled" => $consultantActivity->isCancelled(),
        ];
    }

    protected function buildViewService()
    {
        $consultantActivityRepository = $this->em->getRepository(ConsultantActivity::class);
        return new ViewConsultantActivity($consultantActivityRepository);
    }

    protected function buildInitiateService()
    {
        $consultantActivityRepository = $this->em->getRepository(ConsultantActivity2::class);
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $activityTypeRepository = $this->em->getRepository(ActivityType::class);
        $dispatcher = new Dispatcher();

        return new InitiateActivity($consultantActivityRepository, $consultantRepository, $activityTypeRepository, $dispatcher);
    }

    protected function buildUpdateService()
    {
        $consultantActivityRepository = $this->em->getRepository(ConsultantActivity2::class);
        $dispatcher = new Dispatcher();
        
        return new UpdateActivity($consultantActivityRepository, $dispatcher);
    }

}
