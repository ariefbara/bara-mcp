<?php

namespace Firm\Domain\Model\Firm\Program;

class MetricData
{

    /**
     *
     * @var string|null
     */
    protected $name;

    /**
     *
     * @var string|null
     */
    protected $description;

    /**
     *
     * @var int|null
     */
    protected $minValue;

    /**
     *
     * @var int|null
     */
    protected $maxValue;

    /**
     *
     * @var bool|null
     */
    protected $higherIsBetter;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMinValue(): ?int
    {
        return $this->minValue;
    }

    public function getMaxValue(): ?int
    {
        return $this->maxValue;
    }

    public function getHigherIsBetter(): ?bool
    {
        return $this->higherIsBetter;
    }

    public function __construct(
            ?string $name, ?string $description, ?int $minValue, ?int $maxValue, ?bool $higherIsBetter)
    {
        $this->name = $name;
        $this->description = $description;
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
        $this->higherIsBetter = $higherIsBetter;
    }

}
