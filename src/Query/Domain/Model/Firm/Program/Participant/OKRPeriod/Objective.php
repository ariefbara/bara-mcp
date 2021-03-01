<?php

namespace Query\Domain\Model\Firm\Program\Participant\OKRPeriod;

use Doctrine\Common\Collections\ArrayCollection;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;
use Query\Domain\Model\Firm\Program\Participant\OKRPeriod\Objective\KeyResult;
use SharedContext\Domain\ValueObject\Label;

class Objective
{

    /**
     * 
     * @var OKRPeriod
     */
    protected $okrPeriod;

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
    protected $weight;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var ArrayCollection
     */
    protected $keyResults;

    public function getOkrPeriod(): OKRPeriod
    {
        return $this->okrPeriod;
    }

    public function getId(): string
    {
        return $this->id;
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
    
    /**
     * 
     * @return KeyResult[]
     */
    public function iterateKeyResults()
    {
        return $this->keyResults->getIterator();
    }

}
