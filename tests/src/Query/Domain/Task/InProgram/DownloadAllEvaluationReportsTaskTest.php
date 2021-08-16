<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;

class DownloadAllEvaluationReportsTaskTest extends TaskInProgramTestBase
{
    protected $evaluationReportRepository;
    protected $evaluationReportFilter;
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        $this->evaluationReportFilter = $this->buildMockOfClass(EvaluationReportFilter::class);
        
        $this->task = new DownloadAllEvaluationReportsTask($this->evaluationReportRepository, $this->evaluationReportFilter);
    }
    
    protected function execute()
    {
        $this->task->executeTaskInProgram($this->programId);
    }
    public function test_execute_scenario_expectedResult()
    {
        
    }
}
