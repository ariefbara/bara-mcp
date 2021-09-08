<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Tests\src\Query\Domain\Task\InProgram\TaskInProgramTestBase;

class GenerateProgramEvaluationReportSummaryTaskTest extends TaskInProgramTestBase
{
    protected $evaluationReportRepository, $evaluationReport;
    protected $evaluationReportSummaryFilter;
    protected $programEvaluationReportSummaryResult;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        
        $this->evaluationReportSummaryFilter = $this->buildMockOfClass(EvaluationReportSummaryFilter::class);
        $this->programEvaluationReportSummaryResult = $this->buildMockOfInterface(IProgramEvaluationReportSummaryResult::class);
        
        $this->task = new GenerateProgramEvaluationReportSummaryTask(
                $this->evaluationReportRepository, $this->evaluationReportSummaryFilter, 
                $this->programEvaluationReportSummaryResult);
    }
    
    protected function executeTaskInProgram()
    {
        $this->task->executeTaskInProgram($this->program);
    }
    public function test_executeTaskInProgram_includeAllEvaluationReportFromRepositoryToResult()
    {
        $this->evaluationReportRepository->expects($this->once())
                ->method('allNonPaginatedEvaluationReportsInProgram')
                ->with($this->program, $this->evaluationReportSummaryFilter)
                ->willReturn([$this->evaluationReport]);
        
        $this->programEvaluationReportSummaryResult->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        
        $this->executeTaskInProgram();
    }
}
