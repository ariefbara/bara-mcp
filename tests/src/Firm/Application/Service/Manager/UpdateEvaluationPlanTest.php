<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\{
    FeedbackForm,
    Manager,
    Program\EvaluationPlan,
    Program\EvaluationPlanData
};
use Tests\TestBase;

class UpdateEvaluationPlanTest extends TestBase
{

    protected $evaluationPlanRepository, $evaluationPlan;
    protected $managerRepository, $manager;
    protected $feedbackFormRepository, $feedbackForm;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $evaluationPlanId = "evaluationPlanId", $feedbackFormId = "feedbackFormId";
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

        $this->service = new UpdateEvaluationPlan(
                $this->evaluationPlanRepository, $this->managerRepository, $this->feedbackFormRepository);

        $this->evaluationPlanData = $this->buildMockOfClass(EvaluationPlanData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->managerId, $this->evaluationPlanId, $this->evaluationPlanData,
                $this->feedbackFormId);
    }
    public function test_execute_updateEvaluationPlanByManager()
    {
        $this->manager->expects($this->once())
                ->method("updateEvaluationPlan")
                ->with($this->evaluationPlan, $this->evaluationPlanData, $this->feedbackForm);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->evaluationPlanRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
