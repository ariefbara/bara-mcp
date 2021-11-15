<?php

namespace Query\Domain\Task\Dependency\MentoringFilter;

class MentoringSlotFilter
{

    /**
     * 
     * @var bool|null
     */
    protected $bookingAvailableStatus;

    /**
     * 
     * @var bool|null
     */
    protected $cancelledStatus;

    /**
     * 
     * @var bool|null
     */
    protected $reportCompletedStatus;

    public function getBookingAvailableStatus(): ?bool
    {
        return $this->bookingAvailableStatus;
    }

    public function getCancelledStatus(): ?bool
    {
        return $this->cancelledStatus;
    }

    public function getReportCompletedStatus(): ?bool
    {
        return $this->reportCompletedStatus;
    }

    public function setBookingAvailableStatus(?bool $bookingAvailableStatus)
    {
        $this->bookingAvailableStatus = $bookingAvailableStatus;
        return $this;
    }

    public function setCancelledStatus(?bool $cancelledStatus)
    {
        $this->cancelledStatus = $cancelledStatus;
        return $this;
    }

    public function setReportCompletedStatus(?bool $reportCompletedStatus)
    {
        $this->reportCompletedStatus = $reportCompletedStatus;
        return $this;
    }

    public function __construct()
    {
        
    }

}
