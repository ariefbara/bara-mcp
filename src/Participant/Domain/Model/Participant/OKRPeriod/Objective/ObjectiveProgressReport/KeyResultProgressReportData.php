<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

class KeyResultProgressReportData
{

    /**
     * 
     * @var int|null
     */
    protected $value;

    public function __construct(?int $value)
    {
        $this->value = $value;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

}
