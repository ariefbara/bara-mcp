<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Resources\Domain\ValueObject\IntegerRange;
use Tests\TestBase;

class MetricTest extends TestBase
{
    protected $program;
    protected $metric;
    protected $id = "newID", $name = "new name", $description = "new description", $minValue = 999, $maxValue = 999999, 
            $higherIsBetter = true;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $metricData = new MetricData("name", 'description', null, null, null);
        $this->metric = new TestableMetric($this->program, "id", $metricData);
    }
    
    protected function buildMetricData()
    {
        return new MetricData($this->name, $this->description, $this->minValue, $this->maxValue, $this->higherIsBetter);
    }
    
    protected function executeConstruct()
    {
        return new TestableMetric($this->program, $this->id, $this->buildMetricData());
    }
    public function test_construct_setProperties()
    {
        $metric = $this->executeConstruct();
        $this->assertEquals($this->program, $metric->program);
        $this->assertEquals($this->id, $metric->id);
        $this->assertEquals($this->name, $metric->name);
        $this->assertEquals($this->description, $metric->description);
        $this->assertEquals($this->higherIsBetter, $metric->higherIsBetter);
        
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $metric->minMaxValue);
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = "";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: metric name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    
    protected function executeUpdate()
    {
        $this->metric->update($this->buildMetricData());
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->metric->name);
        $this->assertEquals($this->description, $this->metric->description);
        $this->assertEquals($this->higherIsBetter, $this->metric->higherIsBetter);
        
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $this->metric->minMaxValue);
    }
    
    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->metric->belongsToProgram($this->metric->program));
    }
    public function test_belongsToProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->metric->belongsToProgram($program));
    }
}

class TestableMetric extends Metric
{
    public $program;
    public $id;
    public $name;
    public $description;
    public $minMaxValue;
    public $higherIsBetter;
}
