<?php

namespace Firm\Domain\Model\Firm\Program\Participant\MetricAssignment;

use Firm\Domain\ {
    Model\Firm\Program\Metric,
    Model\Firm\Program\Participant\MetricAssignment,
    Service\MetricAssignmentDataProvider
};
use Tests\TestBase;

class AssignmentFieldTest extends TestBase
{
    protected $metricAssignment;
    protected $metric;
    protected $assignmentField;
    protected $id = "newId";
    protected $target = 999;
    protected $metricAssignmentDataProvider;


    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignment = $this->buildMockOfClass(MetricAssignment::class);
        $this->metric = $this->buildMockOfClass(Metric::class);
        $this->assignmentField = new TestableAssignmentField($this->metricAssignment, "id", $this->metric, 111);
        
        $this->metricAssignmentDataProvider = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableAssignmentField($this->metricAssignment, $this->id, $this->metric, $this->target);
    }
    public function test_construc_scenario_expectedResult()
    {
        $assignmentField = $this->executeConstruct();
        $this->assertEquals($this->metricAssignment, $assignmentField->metricAssignment);
        $this->assertEquals($this->id, $assignmentField->id);
        $this->assertEquals($this->metric, $assignmentField->metric);
        $this->assertEquals($this->target, $assignmentField->target);
        $this->assertFalse($assignmentField->disabled);
    }
    public function test_construct_emptyTarget_badRequest()
    {
        $this->target = 0;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: assignment field target is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    
    protected function executeUpdate()
    {
        $this->metricAssignmentDataProvider->expects($this->any())
                ->method("pullTargetCorrespondWithMetric")
                ->with($this->metric)
                ->willReturn($this->target);
        $this->assignmentField->update($this->metricAssignmentDataProvider);
    }
    public function test_update_updateTarget()
    {
        $this->executeUpdate();
        $this->assertEquals($this->target, $this->assignmentField->target);
    }
    public function test_update_disabledField_setEnable()
    {
        $this->assignmentField->disabled = true;
        $this->executeUpdate();
        $this->assertFalse($this->assignmentField->disabled);
    }
    public function test_update_noTargetInMetricAssignmentDataProviderCorrespondWithMetric_removeAssignmentField()
    {
        $this->metricAssignmentDataProvider->expects($this->once())
                ->method("pullTargetCorrespondWithMetric")
                ->with($this->metric)
                ->willReturn(null);
        $this->executeUpdate();
        $this->assertTrue($this->assignmentField->disabled);
    }
}

class TestableAssignmentField extends AssignmentField
{
    public $metricAssignment;
    public $id;
    public $metric;
    public $target;
    public $disabled;
}
