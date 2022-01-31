<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;

class MentoringRequestData
{

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $startTime;

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

    public function getMediaType(): ?string
    {
        return $this->mediaType;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function __construct(?DateTimeImmutable $startTime, ?string $mediaType, ?string $location)
    {
        $this->startTime = $startTime;
        $this->mediaType = $mediaType;
        $this->location = $location;
    }

}
