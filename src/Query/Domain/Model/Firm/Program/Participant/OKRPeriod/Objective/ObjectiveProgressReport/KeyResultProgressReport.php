<?php

namespace Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\KeyResult;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

class KeyResultProgressReport
{

    /**
     * 
     * @var ObjectiveProgressReport
     */
    protected $objectiveProgressReport;

    /**
     * 
     * @var KeyResult
     */
    protected $keyResult;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var int|null
     */
    protected $value;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    public function getObjectiveProgressReport(): ObjectiveProgressReport
    {
        return $this->objectiveProgressReport;
    }

    public function getKeyResult(): KeyResult
    {
        return $this->keyResult;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }

}
