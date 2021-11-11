<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant;

use DateTimeImmutable;

class MentoringSlotFilter
{

    /**
     * 
     * @var string|null
     */
    protected $consultantId;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $from;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $to;

    /**
     * 
     * @var string|null
     */
    protected $consultationSetupId;

    /**
     * 
     * @var bool|null
     */
    protected $cancelledStatus;

    public function getConsultantId(): ?string
    {
        return $this->consultantId;
    }

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

    public function getConsultationSetupId(): ?string
    {
        return $this->consultationSetupId;
    }

    public function getCancelledStatus(): ?bool
    {
        return $this->cancelledStatus;
    }

    public function setConsultantId(?string $consultantId)
    {
        $this->consultantId = $consultantId;
        return $this;
    }

    public function setFrom(?DateTimeImmutable $from)
    {
        $this->from = $from;
        return $this;
    }

    public function setTo(?DateTimeImmutable $to)
    {
        $this->to = $to;
        return $this;
    }

    public function setConsultationSetupId(?string $consultationSetupId)
    {
        $this->consultationSetupId = $consultationSetupId;
        return $this;
    }

    public function setCancelledStatus(?bool $cancelledStatus)
    {
        $this->cancelledStatus = $cancelledStatus;
        return $this;
    }

    public function __construct()
    {
        
    }

}
