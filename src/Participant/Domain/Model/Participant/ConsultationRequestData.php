<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;

class ConsultationRequestData
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
    protected $media;

    /**
     * 
     * @var string|null
     */
    protected $address;

    function __construct(?DateTimeImmutable $startTime, ?string $media, ?string $address)
    {
        $this->startTime = $startTime;
        $this->media = $media;
        $this->address = $address;
    }

    function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    function getMedia(): ?string
    {
        return $this->media;
    }

    function getAddress(): ?string
    {
        return $this->address;
    }

}
