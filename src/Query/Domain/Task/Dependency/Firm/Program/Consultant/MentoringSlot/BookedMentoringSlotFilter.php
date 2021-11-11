<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlot;

class BookedMentoringSlotFilter
{

    /**
     * 
     * @var string|null
     */
    protected $consultantId;

    /**
     * 
     * @var string|null
     */
    protected $mentoringSlotId;

    /**
     * 
     * @var bool|null
     */
    protected $cancelledStatus;

    public function getConsultantId(): ?string
    {
        return $this->consultantId;
    }

    public function getMentoringSlotId(): ?string
    {
        return $this->mentoringSlotId;
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

    public function setMentoringSlotId(?string $mentoringSlotId)
    {
        $this->mentoringSlotId = $mentoringSlotId;
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
