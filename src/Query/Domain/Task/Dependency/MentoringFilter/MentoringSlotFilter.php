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
    
    public function getSqlBookingAvailableStatusClause(string $bookedCountColumnName, string $capacityColumnName): ?string
    {
        if ($this->bookingAvailableStatus === true) {
            return "AND $bookedCountColumnName < $capacityColumnName";
        } elseif ($this->bookingAvailableStatus === false) {
            return "AND $bookedCountColumnName = $capacityColumnName";
        }
        return null;
    }
    
    public function getSqlCancelldStatusClause(string $tableName, array &$parameters): ?string
    {
        if (isset($this->cancelledStatus)) {
            $parameters['cancelledStatus'] = $this->cancelledStatus;
            return "AND $tableName.cancelled = :cancelledStatus";
        }
        return null;
    }
    
    public function getSqlMentorReportCompletedStatusClause(string $submittedReportCountColumnName, string $bookedCountColumnName): ?string
    {
        if ($this->reportCompletedStatus === true) {
            return "AND $submittedReportCountColumnName = $bookedCountColumnName";
        } elseif ($this->reportCompletedStatus === false) {
            return "AND $submittedReportCountColumnName < $bookedCountColumnName";
        }
        return null;
    }
    
    public function getSqlParticipantReportCompletedStatusClause(string $participantReportIdColumnName): ?string
    {
        if ($this->reportCompletedStatus === true) {
            return "AND $participantReportIdColumnName IS NOT NULL";
            return "AND $submittedReportCountColumnName = $bookedCountColumnName";
        } elseif ($this->reportCompletedStatus === false) {
            return "AND $participantReportIdColumnName IS NULL";
        }
        return null;
    }

}
