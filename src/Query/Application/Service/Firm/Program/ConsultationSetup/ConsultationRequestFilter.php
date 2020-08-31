<?php

namespace Query\Application\Service\Firm\Program\ConsulationSetup;

use DateTimeImmutable;

class ConsultationRequestFilter
{

    /**
     *
     * @var DateTimeImmutable||null
     */
    protected $minStartTime;

    /**
     *
     * @var DateTimeImmutable||null
     */
    protected $maxStartTime;

    public function setMinStartTime(?DateTimeImmutable $minStartTime)
    {
        $this->minStartTime = $minStartTime;
        return $this;
    }

    public function setMaxStartTime(?DateTimeImmutable $maxStartTime)
    {
        $this->maxStartTime = $maxStartTime;
        return $this;
    }

    function getMinStartTime(): ?DateTimeImmutable
    {
        return $this->minStartTime;
    }

    function getMaxStartTime(): ?DateTimeImmutable
    {
        return $this->maxStartTime;
    }

    public function __construct()
    {
        ;
    }

}
