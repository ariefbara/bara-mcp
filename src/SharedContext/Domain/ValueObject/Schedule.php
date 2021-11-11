<?php

namespace SharedContext\Domain\ValueObject;

use DateTimeImmutable;
use Resources\Domain\ValueObject\TimeInterval;

class Schedule extends TimeInterval
{

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $startTime;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $endTime;

    /**
     * 
     * @var string|null
     */
    protected $mediaType;

    /**
     * 
     * @var string|null
     */
    protected $location;
    
    public function getStartTimeString(): string
    {
        return $this->startTime->format('Y-m-d H:i:s');
    }

    public function getEndTimeString(): string
    {
        return $this->endTime->format('Y-m-d H:i:s');
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    protected function getEndTimeStamp(): float
    {
        return $this->endTime->getTimestamp();
    }

    protected function getStartTimeStamp(): float
    {
        return $this->startTime->getTimestamp();
    }
    
    protected function setStartTime(\DateTimeImmutable $startTime): void
    {
        $this->startTime = $startTime;
    }
    
    protected function setEndTime(\DateTimeImmutable $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function __construct(ScheduleData $scheduleData)
    {
        $this->setStartTime($scheduleData->getStartTime());
        $this->setEndTime($scheduleData->getEndTime());
        $this->mediaType = $scheduleData->getMediaType();
        $this->location = $scheduleData->getLocation();
    }
    
    
}
