<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program\Participant\DedicatedMentor\EvaluationReport;
use Query\Domain\Model\Firm\Program\Participant\ParticipantEvaluationReportTranscript;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportTranscriptFilter;
use Tests\src\Query\Domain\Task\InProgram\TaskInProgramTestBase;

class GenerateParticipantEvaluationReportTranscriptTaskTest extends TaskInProgramTestBase
{
    protected $evaluationReportRepository, $evaluationReport;
    protected $participantId = 'participant-id';
    protected $evaluationReportTranscriptFilter;
    protected $task, $result;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReport = $this->buildMockOfClass(EvaluationReport::class);
        $this->evaluationReportRepository = $this->buildMockOfInterface(EvaluationReportRepository::class);
        $this->evaluationReportRepository->expects($this->any())
                ->method('allEvaluationReportsBelongsToParticipantInProgram')
                ->with($this->program, $this->participantId)
                ->willReturn([$this->evaluationReport]);
        
        $this->evaluationReportTranscriptFilter = $this->buildMockOfClass(EvaluationReportTranscriptFilter::class);
        
        $this->task = new TestableGenerateParticipantEvaluationReportTranscriptTask(
                $this->evaluationReportRepository, $this->participantId, $this->evaluationReportTranscriptFilter);
        
        $this->result = $this->buildMockOfClass(ParticipantEvaluationReportTranscript::class);
        $this->task->result = $this->result;
    }
    
    public function test_construct_setParticipantEvaluationReportTranscriptResult()
    {
        $task = new TestableGenerateParticipantEvaluationReportTranscriptTask(
                $this->evaluationReportRepository, $this->participantId, $this->evaluationReportTranscriptFilter);
        $this->assertEquals(new ParticipantEvaluationReportTranscript(), $task->result);
    }
    
    protected function executeTaskInProgram()
    {
        $this->task->executeTaskInProgram($this->program);
    }
    public function test_executeTaskInProgram_includeAllEvaluationReportFromRepositoryToTranscript()
    {
        $this->result->expects($this->once())
                ->method('includeEvaluationReport')
                ->with($this->evaluationReport);
        $this->executeTaskInProgram();
    }
}

class TestableGenerateParticipantEvaluationReportTranscriptTask extends GenerateParticipantEvaluationReportTranscriptTask
{
    public $evaluationReportReporsitory;
    public $participantId;
    public $evaluationReportTranscriptFilter;
    public $result;
}
