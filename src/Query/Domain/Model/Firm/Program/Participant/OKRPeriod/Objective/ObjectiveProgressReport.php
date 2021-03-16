<?php

namespace Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;

class ObjectiveProgressReport
{

    /**
     * 
     * @var Objective
     */
    protected $objective;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $reportDate;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $submitTime;

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
    protected $keyResultProgressReports;

    public function getObjective(): Objective
    {
        return $this->objective;
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

    public function getReportDateString(): string
    {
        return $this->reportDate->format('Y-m-d');
    }

    public function getSubmitTimeString(): string
    {
        return $this->submitTime->format('Y-m-d H:i:s');
    }

    public function getApprovalStatusValue(): int
    {
        return $this->approvalStatus->getValue();
    }

    /**
     * 
     * @return KeyResultProgressReport
     */
    public function iterateKeyResultProgressReports()
    {
        return $this->keyResultProgressReports->getIterator();
    }

}
