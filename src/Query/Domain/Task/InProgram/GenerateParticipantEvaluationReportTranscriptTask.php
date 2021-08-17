<?php

namespace Query\Domain\Task\InProgram;

use Query\Domain\Model\Firm\Program;
use Query\Domain\Model\Firm\Program\ITaskInProgramExecutableByCoordinator;
use Query\Domain\Model\Firm\Program\Participant\ParticipantEvaluationReportTranscript;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportRepository;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor\EvaluationReportTranscriptFilter;

class GenerateParticipantEvaluationReportTranscriptTask implements ITaskInProgramExecutableByCoordinator
{

    /**
     * 
     * @var EvaluationReportRepository
     */
    protected $evaluationReportReporsitory;

    /**
     * 
     * @var string
     */
    protected $participantId;

    /**
     * 
     * @var EvaluationReportTranscriptFilter
     */
    protected $evaluationReportTranscriptFilter;

    /**
     * 
     * @var ParticipantEvaluationReportTranscript
     */
    protected $result;

    public function getResult(): ParticipantEvaluationReportTranscript
    {
        return $this->result;
    }

    public function __construct(
            EvaluationReportRepository $evaluationReportReporsitory, string $participantId,
            EvaluationReportTranscriptFilter $evaluationReportTranscriptFilter)
    {
        $this->evaluationReportReporsitory = $evaluationReportReporsitory;
        $this->participantId = $participantId;
        $this->evaluationReportTranscriptFilter = $evaluationReportTranscriptFilter;
        $this->result = new ParticipantEvaluationReportTranscript();
    }

    public function executeTaskInProgram(Program $program): void
    {
        $evaluationReports = $this->evaluationReportReporsitory->allEvaluationReportsBelongsToParticipantInProgram(
                $program, $this->participantId, $this->evaluationReportTranscriptFilter);

        foreach ($evaluationReports as $evaluationReport) {
            $this->result->includeEvaluationReport($evaluationReport);
        }
    }

}
