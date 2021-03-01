<?php

namespace Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective;
use SharedContext\Domain\ValueObject\Label;

class KeyResult
{

    /**
     * 
     * @var Objective
     */
    protected $objective;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Label
     */
    protected $label;

    /**
     * 
     * @var int
     */
    protected $target;

    /**
     * 
     * @var int
     */
    protected $weight;

    /**
     * 
     * @var bool 
     */
    protected $disabled;

    public function getObjective(): Objective
    {
        return $this->objective;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTarget(): int
    {
        return $this->target;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function __construct()
    {
        
    }

    public function getName(): string
    {
        return $this->label->getName();
    }

    public function getDescription(): ?string
    {
        return $this->label->getDescription();
    }

}
