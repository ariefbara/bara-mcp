<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Resources\Domain\ValueObject\IntegerRange;
use Tests\TestBase;

class MetricTest extends TestBase
{
    protected $metric;
    protected $minMaxValue;
    protected $value = 999.99;


    protected function setUp(): void
    {
        parent::setUp();
        $this->metric = new TestableMetric();
        
        $this->minMaxValue = $this->buildMockOfClass(IntegerRange::class);
        $this->metric->minMaxValue = $this->minMaxValue;
    }
    
    public function test_isValueAcceptable_returnMinMaxValueContainResult()
    {
        $this->minMaxValue->expects($this->once())
                ->method("contain")
                ->with($this->value);
        $this->metric->isValueAcceptable($this->value);
    }
}

class TestableMetric extends Metric
{
    public $program;
    public $id;
    public $minMaxValue;
    
    function __construct()
    {
        parent::__construct();
    }
}
