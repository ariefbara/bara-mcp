<?php

namespace Firm\Domain\Model\Firm\Program\MeetingType;

use DateTimeImmutable;

class MeetingData
{

    /**
     *
     * @var string|null
     */
    protected $name;

    /**
     *
     * @var string|null
     */
    protected $description;

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
    protected $location;

    /**
     *
     * @var string|null
     */
    protected $note;

    function __construct(?string $name, ?string $description, ?DateTimeImmutable $startTime,
            ?DateTimeImmutable $endTime, ?string $location, ?string $note)
    {
        $this->name = $name;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->location = $location;
        $this->note = $note;
    }

    function getName(): ?string
    {
        return $this->name;
    }

    function getDescription(): ?string
    {
        return $this->description;
    }

    function getStartTime(): ?DateTimeImmutable
    {
        return $this->startTime;
    }

    function getEndTime(): ?DateTimeImmutable
    {
        return $this->endTime;
    }

    function getLocation(): ?string
    {
        return $this->location;
    }

    function getNote(): ?string
    {
        return $this->note;
    }

}
