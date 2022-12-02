<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

class TaskListFilterForCoordinator
{

    /**
     * 
     * @var TaskListFilter
     * 
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
    protected $onlyShowOwnedTask = false;

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
    
    public function setOnlyShowOwnedTask()
    {
        $this->onlyShowOwnedTask = true;
        return $this;
    }

    public function __construct(TaskListFilter $taskListFilter)
    {
        $this->taskListFilter = $taskListFilter;
    }
    
    protected function getProgramIdCritera(&$parameters): ?string
    {
        if (empty($this->programId)) {
            return null;
        }
        $parameters['programId'] = $this->programId;
        return <<<_STATEMENT
    AND Participant.Program_id = :programId
_STATEMENT;
    }
    protected function getParticipantIdCriteria(&$parameters): ?string
    {
        if (empty($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return <<<_STATEMENT
    AND Task.Participant_id = :participantId
_STATEMENT;
    }
    protected function getOnlyShowOwnedTaskCriteria(): ?string
    {
        return !$this->onlyShowOwnedTask ? null : <<<_CRITERIA
    AND _coordinatorTaskGiver.Personnel_id = :personnelId
_CRITERIA;
    }

    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->taskListFilter->getOptionalConditionStatement($parameters)
                . $this->getProgramIdCritera($parameters)
                . $this->getParticipantIdCriteria($parameters)
                . $this->getOnlyShowOwnedTaskCriteria();
    }

    public function getOrderStatement(): ?string
    {
        return $this->taskListFilter->getOrderStatement();
    }

    public function getLimitStatement(): ?string
    {
        return $this->taskListFilter->getLimitStatement();
    }

}
