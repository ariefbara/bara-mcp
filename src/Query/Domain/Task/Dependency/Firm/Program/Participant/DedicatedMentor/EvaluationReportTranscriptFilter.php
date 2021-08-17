<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant\DedicatedMentor;

class EvaluationReportTranscriptFilter
{

    /**
     * 
     * @var array
     */
    protected $evaluationPlanIdList;

    /**
     * 
     * @var array
     */
    protected $mentorIdList;

    public function getEvaluationPlanIdList(): array
    {
        return $this->evaluationPlanIdList;
    }

    public function getMentorIdList(): array
    {
        return $this->mentorIdList;
    }

    public function __construct()
    {
        $this->evaluationPlanIdList = [];
        $this->mentorIdList = [];
    }
    
    public function addEvaluationPlanId(string $evaluationPlanId): void
    {
        $this->evaluationPlanIdList[] = $evaluationPlanId;
    }

    public function addMentorId(string $mentorId): void
    {
        $this->mentorIdList[] = $mentorId;
    }
}
