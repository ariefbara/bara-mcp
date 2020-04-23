<?php
namespace Resources\Domain\ValueObject;

use Resources\Exception\RegularException;

class IntegerRange
{

    protected $minValue = null;

    protected $maxValue = null;

    
    public function getMinValue(): ?int
    {
        return $this->minValue;
    }

    public function getMaxValue(): ?int
    {
        return $this->maxValue;
    }

    public function __construct(?int $minValue, ?int $maxValue)
    {
        $this->minValue = $minValue;
        $this->maxValue = $maxValue;
        $this->assertMaxValueBiggerThanOrEqualsMinValue();
    }

    public function sameValueAs(IntegerRange $other): bool
    {
        return $this->minValue == $other->minValue
            && $this->maxValue = $other->maxValue;
    }
    
    public function contain(int $value): bool {
        return $this->actualMinValue() <= $value
            && $this->actualMaxValue() >= $value;
    }
    
    protected function actualMinValue() {
        return empty($this->minValue)? -INF: $this->minValue;
    }
    protected function actualMaxValue() {
        return empty($this->maxValue)? INF: $this->maxValue;
    }
    protected function assertMaxValueBiggerThanOrEqualsMinValue()
    {
        if ($this->actualMaxValue() < $this->actualMinValue()) {
            $errorDetail = "bad request: max value must be bigger or equals  min value";
            throw RegularException::badRequest($errorDetail);
        }
    }
}

