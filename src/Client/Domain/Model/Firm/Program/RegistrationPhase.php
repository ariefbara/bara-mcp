<?php

namespace Client\Domain\Model\Firm\Program;

use Client\Domain\Model\Firm\Program;
use DateTimeImmutable;
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

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct()
    {
        ;
    }

    public function isOpen(): bool
    {
        return $this->startEndDate->contain(new DateTimeImmutable());
    }

}
