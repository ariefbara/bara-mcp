<?php

namespace Personnel\Domain\Task\DedicatedMentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor\EvaluationReport;
use Tests\src\Personnel\Domain\Task\DedicatedMentor\EvaluationReportTestBase;

class CancelEvaluationReportTest extends EvaluationReportTestBase
{
    protected $evaluationReportRepository, $evaluationReport, $id = 'evaluation-report-id';
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        $this->evaluationReportRepository->expects($this->any())
                ->method('ofId')
                ->with($this->id)
                ->willReturn($this->evaluationReport);
        
        $this->task = new CancelEvaluationReport($this->evaluationReportRepository, $this->id);
    }
    
    protected function execute()
    {
        $this->task->execute($this->dedicatedMentor);
    }
    public function test_execute_cancelEvaluationReport()
    {
        $this->evaluationReport->expects($this->once())
                ->method('cancel');
        $this->execute();
    }
    public function test_execute_assertEvaluationReportInManageableByDedicatedMentor()
    {
        $this->evaluationReport->expects($this->once())
                ->method('assertManageableByDedicatedMentor')
                ->with($this->dedicatedMentor);
        $this->execute();
    }
}
