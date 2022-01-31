<?php

namespace SharedContext\Domain\ValueObject;

use DateTimeImmutable;

class ScheduleData
{

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $startTime;

    /**
     * 
     * @var DateTimeImmutable|null
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

    public function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    public function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function __construct(
            ?DateTimeImmutable $startTime, ?DateTimeImmutable $endTime, ?string $mediaType, ?string $location)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->mediaType = $mediaType;
        $this->location = $location;
    }

}
