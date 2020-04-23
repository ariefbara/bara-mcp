<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;

class RegistrationPhaseData
{

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $startDate;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $endDate;

    function getName(): ?string
    {
        return $this->name;
    }

    function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    function __construct(?string $name, ?DateTimeImmutable $startDate, ?DateTimeImmutable $endDate)
    {
        $this->name = $name;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

}
