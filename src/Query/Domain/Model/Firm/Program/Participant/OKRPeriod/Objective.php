<?php

namespace Query\Domain\Model\Firm\Program\Participant\OKRPeriod;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\KeyResult;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;

class Objective
{

    /**
     * 
     * @var OKRPeriod
     */
    protected $okrPeriod;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Label
     */
    protected $label;

    /**
     * 
     * @var int
     */
    protected $weight;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $keyResults;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $objectiveProgressReports;

    public function getOkrPeriod(): OKRPeriod
    {
        return $this->okrPeriod;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }
    
    public function getName(): string
    {
        return $this->label->getName();
    }

    public function getDescription(): ?string
    {
        return $this->label->getDescription();
    }
    
    /**
     * 
     * @return KeyResult[]
     */
    public function iterateKeyResults()
    {
        return $this->keyResults->getIterator();
    }
    
    public function getLastApprovedProgressReport(): ?ObjectiveProgressReport
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('status', OKRPeriodApprovalStatus::APPROVED))
                ->orderBy(['reportDate' => Criteria::DESC]);
        
        $progressReport = $this->objectiveProgressReports->matching($criteria)->first();
        return empty($progressReport) ? null : $progressReport;
    }

}
