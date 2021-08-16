<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Program\ProgramMentorEvaluationReportSummary;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportSummaryFilter;
use Tests\src\Query\Domain\Task\InProgram\TaskInProgramTestBase;

class GenerateProgramMentorEvaluationReportSummaryTaskTest extends TaskInProgramTestBase
{

    protected $evaluationReportRepository, $evaluationReport;
    protected $evaluationReportSummaryFilter;
    protected $task;
    protected $result;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        $this->evaluationReportRepository->expects($this->any())
                ->method('allNonPaginatedEvaluationReportsInProgram')
                ->with($this->program)
                ->willReturn([$this->evaluationReport]);

        $this->evaluationReportSummaryFilter = $this->buildMockOfClass(EvaluationReportSummaryFilter::class);

        $this->task = new TestableGenerateProgramMentorEvaluationReportSummaryTask(
                $this->evaluationReportRepository, $this->evaluationReportSummaryFilter);
        $this->result = $this->buildMockOfClass(ProgramMentorEvaluationReportSummary::class);
        $this->task->result = $this->result;
    }

    protected function executeTaskInProgram()
    {
        $this->task->executeTaskInProgram($this->program);
    }

    public function test_executeTaskInProgram_includeAllEvaluationReportFromRepositoryToSummary()
    {
        $this->result->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->executeTaskInProgram();
    }

}

class TestableGenerateProgramMentorEvaluationReportSummaryTask extends GenerateProgramMentorEvaluationReportSummaryTask
{
    public $result;
}
