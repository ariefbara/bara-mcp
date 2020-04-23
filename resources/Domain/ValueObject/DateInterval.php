<?php
namespace Resources\Domain\ValueObject;

use Resources\Exception\RegularException;

class DateInterval extends TimeInterval
{
    protected $startDate = null, $endDate = null;
    
    public function getStartDateString(): ?string
    {
        return empty($this->startDate)? null: $this->startDate->format("Y-m-d");
    }
    public function getEndDateString(): ?string
    {
        return empty($this->endDate)? null: $this->endDate->format("Y-m-d");
    }
    
    public function getStartDate():?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }
    
    protected function setStartDate(?\DateTimeImmutable $startDate) {
        if (!is_null($startDate)) {
            $year = $startDate->format("Y");
            $month = $startDate->format("m");
            $date = $startDate->format("d");
            $this->startDate = (new \DateTimeImmutable())
                ->setDate($year, $month, $date)
                ->setTime(null, null, null);
        }
    }
    protected function setEndDate(?\DateTimeImmutable $endDate) {
        if (!empty($endDate)) {
            $year = $endDate->format("Y");
            $month = $endDate->format("m");
            $date = $endDate->format("d");
            $this->endDate = (new \DateTimeImmutable())
                ->setDate($year, $month, $date)
                ->setTime(23, 59, 59);
        }
    }
    public function __construct(?\DateTimeImmutable $startDate, ?\DateTimeImmutable $endDate) {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
        if ($this->getStartTimeStamp() > $this->getEndTimeStamp()) {
            $errorDetail = "bad request: end date must be bigger than or equals start date";
            throw RegularException::badRequest($errorDetail);
        }
    }
    
    protected function getStartTimeStamp(): float
    {
        return is_null($this->startDate)? -INF: $this->startDate->getTimestamp();
    }

    protected function getEndTimeStamp(): float
    {
        return is_null($this->endDate)? INF: $this->endDate->getTimestamp();
    }
    
}

