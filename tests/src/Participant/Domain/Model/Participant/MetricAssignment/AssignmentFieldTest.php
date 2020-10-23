<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use Participant\Domain\DependencyModel\Firm\Program\Metric;
use Tests\TestBase;

class AssignmentFieldTest extends TestBase
{
    protected $assignmentField;
    protected $metric;

    protected $metricAssignmentReport;
    protected $metricAssignmentReportData;
    protected $value = 999.0;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assignmentField = new TestableAssignmentField();
        
        $this->metric = $this->buildMockOfClass(Metric::class);
        $this->assignmentField->metric = $this->metric;
        
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        $this->metricAssignmentReportData = $this->buildMockOfClass(MetricAssignmentReportData::class);
        $this->metricAssignmentReportData->expects($this->any())
                ->method("getValueCorrespondWithAssignmentField")
                ->with($this->assignmentField->id)
                ->willReturn($this->value);
    }
    
    protected function executeSetValueIn()
    {
        $this->metric->expects($this->any())
                ->method("isValueAcceptable")
                ->willReturn(true);
        $this->assignmentField->setValueIn($this->metricAssignmentReport, $this->metricAssignmentReportData);
    }
    public function test_setValueIn_setMetricAssignmentReportsAssignmentFieldValue()
    {
        $this->metricAssignmentReport->expects($this->once())
                ->method("setAssignmentFieldValue")
                ->with($this->assignmentField, $this->value);
        $this->executeSetValueIn();
    }
    public function test_setValueId_valueOutOfMetricAllowedValue_forbidden()
    {
        $this->metric->expects($this->once())
                ->method("isValueAcceptable")
                ->with($this->value)
                ->willReturn(false);
        $operation = function (){
            $this->executeSetValueIn();
        };
        $errorDetail = "forbidden: value is out of bound";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableAssignmentField extends AssignmentField
{
    public $metricAssignment;
    public $id = "assignmentFieldId";
    public $metric;
    public $target;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
