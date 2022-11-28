<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

class ParticipantSummaryListFilterForCoordinator
{

    /**
     * 
     * @var ParticipantSummaryListFilter
     */
    protected $participantSummaryListFilter;

    /**
     * 
     * @var string|null
     */
    protected $programId;

    public function setProgramId(?string $programId)
    {
        $this->programId = $programId;
        return $this;
    }

    public function __construct(ParticipantSummaryListFilter $participantSummaryListFilter)
    {
        $this->participantSummaryListFilter = $participantSummaryListFilter;
    }
    //
    protected function getProgramCriteria(&$parameters): ?string
    {
        if (empty($this->programId)) {
            return null;
        }
        $parameters['programId'] = $this->programId;
        return <<<_STATEMENT
    AND Participant.Program_id = :programId
_STATEMENT;
    }
    
    //
    public function getCriteriaStatement(&$parameters): ?string
    {
        return $this->participantSummaryListFilter->getCriteriaStatement($parameters)
                . $this->getProgramCriteria($parameters);
    }

    public function getOrderStatement(): ?string
    {
        return $this->participantSummaryListFilter->getOrderStatement();
    }

    public function getLimitStatement(): ?string
    {
        return $this->participantSummaryListFilter->getLimitStatement();
    }

}
