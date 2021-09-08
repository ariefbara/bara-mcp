<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Tests\src\Query\Domain\Task\InFirm\TaskInFirmTestBase;

class GenerateFirmEvaluationReportSummaryTaskTest extends TaskInFirmTestBase
{
    protected $evaluationReportRepository, $evaluationReport;
    protected $evaluationReportSummaryFilter;
    protected $firmEvaluationReportSummaryResult;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        
        $this->evaluationReportSummaryFilter = $this->buildMockOfClass(EvaluationReportSummaryFilter::class);
        $this->firmEvaluationReportSummaryResult = $this->buildMockOfInterface(IFirmEvaluationReportSummaryResult::class);
        
        $this->task = new GenerateFirmEvaluationReportSummaryTask(
                $this->evaluationReportRepository, $this->evaluationReportSummaryFilter, 
                $this->firmEvaluationReportSummaryResult);
    }
    
    protected function executeTaskInFirm()
    {
        $this->task->executeTaskInFirm($this->firm);
    }
    
    public function test_executeTaskInFirm_includeAllEvaluationReportFromRepositoryToResult()
    {
        $this->evaluationReportRepository->expects($this->once())
                ->method('allNonPaginatedEvaluationReportsInFirm')
                ->with($this->firm, $this->evaluationReportSummaryFilter)
                ->willReturn([$this->evaluationReport]);
        
        $this->firmEvaluationReportSummaryResult->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        
        $this->executeTaskInFirm();
    }
}
