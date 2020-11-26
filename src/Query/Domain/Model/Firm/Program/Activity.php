<?php

namespace Query\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Query\Domain\Model\Firm\{
    Manager\ManagerActivity,
    Program,
    Program\Consultant\ConsultantActivity,
    Program\Coordinator\CoordinatorActivity,
    Program\Participant\ParticipantActivity
};
use Resources\Domain\ValueObject\DateTimeInterval;

class Activity
{

    /**
     *
     * @var ActivityType
     */
    protected $activityType;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var string|null
     */
    protected $description;

    /**
     *
     * @var DateTimeInterval
     */
    protected $startEndTime;

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

    /**
     *
     * @var bool
     */
    protected $cancelled;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $createdTime;

    function getActivityType(): ActivityType
    {
        return $this->activityType;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): ?string
    {
        return $this->description;
    }

    function getLocation(): ?string
    {
        return $this->location;
    }

    function getNote(): ?string
    {
        return $this->note;
    }

    function isCancelled(): bool
    {
        return $this->cancelled;
    }

    function getCreatedTimeString(): string
    {
        return $this->createdTime->format("Y-m-d H:i:s");
    }

    protected function __construct()
    {
        
    }

    function getStartTimeString(): string
    {
        return $this->startEndTime->getStartTime()->format("Y-m-d H:i:s");
    }

    function getEndTimeString(): string
    {
        return $this->startEndTime->getEndTime()->format("Y-m-d H:i:s");
    }

}
