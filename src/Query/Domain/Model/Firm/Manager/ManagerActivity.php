<?php

namespace Query\Domain\Model\Firm\Manager;

use Query\Domain\Model\Firm\ {
    Manager,
    Program,
    Program\Activity,
    Program\ActivityType
};

class ManagerActivity
{

    /**
     *
     * @var Manager
     */
    protected $manager;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Activity
     */
    protected $activity;

    function getManager(): Manager
    {
        return $this->manager;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        
    }

    function getProgram(): Program
    {
        return $this->activity->getProgram();
    }

    function getActivityType(): ActivityType
    {
        return $this->activity->getActivityType();
    }

    function getName(): string
    {
        return $this->activity->getName();
    }

    function getDescription(): ?string
    {
        return $this->activity->getDescription();
    }

    function getLocation(): ?string
    {
        return $this->activity->getLocation();
    }

    function getNote(): ?string
    {
        return $this->activity->getNote();
    }

    function isCancelled(): bool
    {
        return $this->activity->isCancelled();
    }

    function getCreatedTimeString(): string
    {
        return $this->activity->getCreatedTimeString();
    }

    function getStartTimeString(): string
    {
        return $this->activity->getStartTimeString();
    }

    function getEndTimeString(): string
    {
        return $this->activity->getEndTimeString();
    }

}
