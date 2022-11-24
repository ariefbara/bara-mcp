<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

class WorksheetListFilterForCoordinator
{

    /**
     * 
     * @var WorksheetListFilter
     */
    protected $worksheetListFilter;

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

    public function __construct(WorksheetListFilter $worksheetListFilter)
    {
        $this->worksheetListFilter = $worksheetListFilter;
    }
    
    //
    protected function getProgramIdCriteriaStatement(&$parameters): ?string
    {
        if (is_null($this->programId)) {
            return null;
        }
        $parameters['programId'] = $this->programId;
        return <<<_STATEMENT
    AND Participant.Program_id = :programId
_STATEMENT;
    }

    protected function getParticipantIdCriteriaStatement(&$parameters): ?string
    {
        if (is_null($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return <<<_STATEMENT
    AND Participant.id = :participantId
_STATEMENT;
    }
    
    //
    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->worksheetListFilter->getOptionalConditionStatement($parameters)
                . $this->getProgramIdCriteriaStatement($parameters)
                . $this->getParticipantIdCriteriaStatement($parameters);
    }

    public function getOrderStatement(): ?string
    {
        return $this->worksheetListFilter->getOrderStatement();
    }

    public function getLimitStatement(): ?string
    {
        return $this->worksheetListFilter->getLimitStatement();
    }

}
