<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Resources\ {
    Domain\ValueObject\IntegerRange,
    ValidationRule,
    ValidationService
};

class Metric implements AssetInProgram
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

    public function setName(string $name)
    {
        $errorDetail = "bad request: metric name is mandatory";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

        public function __construct(Program $program, string $id, MetricData $metricData)
    {
        $this->program = $program;
        $this->id = $id;
        $this->setName($metricData->getName());
        $this->description = $metricData->getDescription();
        $this->minMaxValue = new IntegerRange($metricData->getMinValue(), $metricData->getMaxValue());
        $this->higherIsBetter = $metricData->getHigherIsBetter();
    }

    public function update(MetricData $metricData): void
    {
        $this->setName($metricData->getName());
        $this->description = $metricData->getDescription();
        $this->minMaxValue = new IntegerRange($metricData->getMinValue(), $metricData->getMaxValue());
        $this->higherIsBetter = $metricData->getHigherIsBetter();
    }

    public function belongsToProgram(Program $program): bool
    {
        return $this->program === $program;
    }

}
