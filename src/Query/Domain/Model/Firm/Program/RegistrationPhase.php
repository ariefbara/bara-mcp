<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program;
use Resources\Domain\ValueObject\DateInterval;

class RegistrationPhase
{

    /**
     *
     * @var Program
     */
    protected $program;

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
     * @var DateInterval
     */
    protected $startEndDate;

    /**
     *
     * @var bool
     */
    protected $removed;

    function getProgram(): Program
    {
        return $this->program;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    function getStartDate(): string
    {
        return $this->startEndDate->getStartDateString();
    }

    function getEndDate(): string
    {
        return $this->startEndDate->getEndDateString();
    }

}
