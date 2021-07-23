<?php

namespace Tests\src\Personnel\Domain\Task\DedicatedMentor;

use Personnel\Domain\Model\Firm\Program\EvaluationPlan;
use Personnel\Domain\Task\Dependency\Firm\Program\EvaluationPlanRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\src\Personnel\Domain\Task\DedicatedMentor\TaskExecutableByDedicatedMentorTestBase;

class EvaluationReportTestBase extends TaskExecutableByDedicatedMentorTestBase
{

    /**
     * 
     * @var MockObject
     */
    protected $evaluationPlanRepository;
    protected $evaluationPlan, $evaluationPlanId = 'evaluationPlanId';

    protected function setUp(): void
    {
        parent::setUp();

        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->evaluationPlanRepository = $this->buildMockOfInterface(EvaluationPlanRepository::class);
        $this->evaluationPlanRepository->expects($this->any())
                ->method('ofId')
                ->with($this->evaluationPlanId)
                ->willReturn($this->evaluationPlan);
    }

}
