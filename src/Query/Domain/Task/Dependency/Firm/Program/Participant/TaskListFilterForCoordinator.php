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
    protected $participantId;

    public function setParticipantId(?string $participantId)
    {
        $this->participantId = $participantId;
        return $this;
    }

    public function __construct(TaskListFilter $taskListFilter)
    {
        $this->taskListFilter = $taskListFilter;
    }

    protected function getOptionalParticipantStatement(&$parameters): ?string
    {
        if (empty($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return <<<_STATEMENT
    AND Task.Participant_id = :participantId
_STATEMENT;
    }

    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->taskListFilter->getOptionalConditionStatement($parameters)
                . $this->getOptionalParticipantStatement($parameters);
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
