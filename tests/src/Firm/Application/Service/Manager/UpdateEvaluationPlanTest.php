<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\FeedbackForm;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program\EvaluationPlan;
use Firm\Domain\Model\Firm\Program\EvaluationPlanData;
use Firm\Domain\Model\Firm\Program\Mission;
use Tests\TestBase;

class UpdateEvaluationPlanTest extends TestBase
{

    protected $evaluationPlanRepository, $evaluationPlan;
    protected $managerRepository, $manager;
    protected $feedbackFormRepository, $feedbackForm;
    protected $missionRepository, $mission;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $evaluationPlanId = "evaluationPlanId", 
            $feedbackFormId = "feedbackFormId", $missionId = 'mission-id';
    protected $evaluationPlanData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationPlanRepository = $this->buildMockOfInterface(EvaluationPlanRepository::class);
        $this->evaluationPlanRepository->expects($this->any())
                ->method("ofId")
                ->with($this->evaluationPlanId)
                ->willReturn($this->evaluationPlan);

        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->managerRepository = $this->buildMockOfInterface(ManagerRepository::class);
        $this->managerRepository->expects($this->any())
                ->method("aManagerInFirm")
                ->with($this->firmId, $this->managerId)
                ->willReturn($this->manager);

        $this->feedbackForm = $this->buildMockOfClass(FeedbackForm::class);
        $this->feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $this->feedbackFormRepository->expects($this->any())
                ->method("aFeedbackFormOfId")
                ->with($this->feedbackFormId)
                ->willReturn($this->feedbackForm);
        
        $this->mission = $this->buildMockOfClass(Mission::class);
        $this->missionRepository = $this->buildMockOfInterface(MissionRepository::class);
        $this->missionRepository->expects($this->any())
                ->method('aMissionOfId')
                ->with($this->missionId)
                ->willReturn($this->mission);

        $this->service = new UpdateEvaluationPlan(
                $this->evaluationPlanRepository, $this->managerRepository, $this->feedbackFormRepository, $this->missionRepository);

        $this->evaluationPlanData = $this->buildMockOfClass(EvaluationPlanData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->managerId, $this->evaluationPlanId, $this->evaluationPlanData,
                $this->feedbackFormId, $this->missionId);
    }
    public function test_execute_updateEvaluationPlanByManager()
    {
        $this->manager->expects($this->once())
                ->method("updateEvaluationPlan")
                ->with($this->evaluationPlan, $this->evaluationPlanData, $this->feedbackForm, $this->mission);
        $this->execute();
    }
    public function test_execute_nullMissionId_updateEvaluationPlanWithNullMission()
    {
        $this->missionId = null;
        $this->manager->expects($this->once())
                ->method("updateEvaluationPlan")
                ->with($this->evaluationPlan, $this->evaluationPlanData, $this->feedbackForm, null);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->evaluationPlanRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
