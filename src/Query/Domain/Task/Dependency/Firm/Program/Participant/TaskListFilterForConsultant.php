<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

class TaskListFilterForConsultant
{

    /**
     * 
     * @var TaskListFilter
     */
    protected $taskListFilter;

    /**
     * 
     * @var string|null
     */
    protected $programId;

    /**
     * 
     * @var string|null
     */
    protected $participantId;
    
    /**
     * 
     * @var bool
     */
    protected $onlyShowRelevantTask = false;

    public function setProgramId(?string $programId)
    {
        $this->programId = $programId;
        return $this;
    }
    public function setParticipantId(?string $participantId)
    {
        $this->participantId = $participantId;
        return $this;
    }
    public function setOnlyShowRelevantTask()
    {
        $this->onlyShowRelevantTask = true;
        return $this;
    }
    
    public function __construct(TaskListFilter $taskListFilter)
    {
        $this->taskListFilter = $taskListFilter;
    }

    protected function getProgramIdCriteria(&$parameters): ?string
    {
        if (empty($this->programId)) {
            return null;
        }
        $parameters['programId'] = $this->programId;
        return <<<_STATEMENT
    AND Participant.Program_id = :programId
_STATEMENT;
    }
    protected function getOptionalAndParticipantStatement(&$parameters): ?string
    {
        if (empty($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return <<<_STATEMENT
    AND Task.Participant_id = :participantId
_STATEMENT;
    }
    protected function getOnlyShowRelevantTaskCriteria(): ?string
    {
        return !$this->onlyShowRelevantTask ? null : <<<_CRITERIA
    AND (
        DedicatedMentor.id IS NOT NULL
        OR _consultantTaskGiver.Personnel_id = :personnelId
    )
_CRITERIA;
    }

    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->taskListFilter->getOptionalConditionStatement($parameters)
                . $this->getProgramIdCriteria($parameters)
                . $this->getOnlyShowRelevantTaskCriteria()
                . $this->getOptionalAndParticipantStatement($parameters);
    }

    public function getOrderStatement(): ?string
    {
        return $this->taskListFilter->getOrderStatement();
    }

    public function getLimitStatement(): ?string
    {
        return $this->taskListFilter->getLimitStatement();
    }

    public function getOptionalParticipantOrStatement(&$parameters): ?string
    {
        if (empty($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return " OR Task.Participant_id = :participantId";
    }

}
