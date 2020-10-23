<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Participant\Domain\DependencyModel\Firm\Program;
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
     * @var IntegerRange
     */
    protected $minMaxValue;
    
    protected function __construct()
    {
        
    }
    
    public function isValueAcceptable(?float $value): bool
    {
        return $this->minMaxValue->contain($value);
    }

}
