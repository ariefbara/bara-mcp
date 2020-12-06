<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

class EvaluationData
{

    /**
     *
     * @var string|null
     */
    protected $status;

    /**
     *
     * @var int|null
     */
    protected $extendDays;

    function __construct(?string $status, ?int $extendDays)
    {
        $this->status = $status;
        $this->extendDays = $extendDays;
    }

    function getStatus(): ?string
    {
        return $this->status;
    }

    function getExtendDays(): ?int
    {
        return $this->extendDays;
    }

}
