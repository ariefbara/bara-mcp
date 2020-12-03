<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\ {
    Manager,
    Program\EvaluationPlan
};
use Tests\TestBase;

class DisableEvaluationPlanTest extends TestBase
{
    protected $evaluationPlanRepository, $evaluationPlan;
    protected $managerRepository, $manager;
    protected $service;
    protected $firmId = "firmId", $managerId = "managerId", $evaluationPlanId = "evaluationPlanId";

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

        $this->service = new DisableEvaluationPlan($this->evaluationPlanRepository, $this->managerRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->managerId, $this->evaluationPlanId);
    }
    public function test_execute_managerDisableEvaluationPlan()
    {
        $this->manager->expects($this->once())
                ->method("disableEvaluationPlan")
                ->with($this->evaluationPlan);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->evaluationPlanRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
