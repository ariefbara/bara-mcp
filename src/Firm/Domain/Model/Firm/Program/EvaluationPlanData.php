<?php

namespace Firm\Domain\Model\Firm\Program;

class EvaluationPlanData
{

    /**
     *
     * @var string|null
     */
    protected $name;

    /**
     *
     * @var int|null
     */
    protected $interval;

    function __construct(?string $name, ?int $interval)
    {
        $this->name = $name;
        $this->interval = $interval;
    }

    function getName(): ?string
    {
        return $this->name;
    }

    function getInterval(): ?int
    {
        return $this->interval;
    }

}
