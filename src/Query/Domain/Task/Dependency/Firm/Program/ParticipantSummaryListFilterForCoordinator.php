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
