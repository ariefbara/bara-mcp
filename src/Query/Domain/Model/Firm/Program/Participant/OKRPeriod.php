<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;
use Resources\Domain\ValueObject\DateInterval;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;

class OKRPeriod
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

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
     * @var DateInterval
     */
    protected $period;
    
    /**
     * 
     * @var OKRPeriodApprovalStatus
     */
    protected $approvalStatus;

    /**
     * 
     * @var bool
     */
    protected $cancelled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $objectives;

    public function getParticipant(): Participant
    {
        return $this->participant;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isCancelled(): bool
    {
        return $this->cancelled;
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

    public function getStartDateString(): ?string
    {
        return $this->period->getStartDateString();
    }

    public function getEndDateString(): ?string
    {
        return $this->period->getEndDateString();
    }
    
    public function getApprovalStatusValue(): ?int
    {
        return $this->approvalStatus->getValue();
    }

    /**
     * 
     * @return Objective[]
     */
    public function iterateObjectives()
    {
        return $this->objectives->getIterator();
    }
}
