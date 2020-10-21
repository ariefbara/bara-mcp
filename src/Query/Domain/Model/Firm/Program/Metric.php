<?php

namespace Query\Domain\Model\Firm\Program;

use Query\Domain\Model\Firm\Program;
use Resources\Domain\ValueObject\IntegerRange;

class Metric
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
     * @var string|null
     */
    protected $description;

    /**
     *
     * @var IntegerRange
     */
    protected $minMaxValue;

    /**
     *
     * @var bool|null
     */
    protected $higherIsBetter;

    public function getProgram(): Program
    {
        return $this->program;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getHigherIsBetter(): ?bool
    {
        return $this->higherIsBetter;
    }

    protected function __construct()
    {
        
    }

    public function getMinValue(): ?int
    {
        return $this->minMaxValue->getMinValue();
    }

    public function getMaxValue(): ?int
    {
        return $this->minMaxValue->getMaxValue();
    }

}
