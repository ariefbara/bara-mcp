<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use Participant\Domain\ {
    DependencyModel\Firm\Program\Metric,
    Service\MetricAssignmentReportDataProvider
};
use Tests\TestBase;

class AssignmentFieldTest extends TestBase
{
    protected $assignmentField;
    protected $metric;

    protected $metricAssignmentReport;
    protected $metricAssignmentReportDataProvider;
    protected $assignmentFieldValueData, $value = 99.99;

    protected function setUp(): void
    {
        parent::setUp();
        $this->assignmentField = new TestableAssignmentField();
        
        $this->metric = $this->buildMockOfClass(Metric::class);
        $this->assignmentField->metric = $this->metric;
        
        $this->assignmentFieldValueData = new MetricAssignmentReport\AssignmentFieldValueData($this->value, "", null);
        
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
    }
    
    protected function executeSetValueIn()
    {
        $this->metricAssignmentReportDataProvider->expects($this->any())
                ->method("getAssignmentFieldValueDataCorrespondWithAssignmentField")
                ->with($this->assignmentField->id)
                ->willReturn($this->assignmentFieldValueData);
        $this->metric->expects($this->any())
                ->method("isValueAcceptable")
                ->willReturn(true);
        $this->assignmentField->setValueIn($this->metricAssignmentReport, $this->metricAssignmentReportDataProvider);
    }
    public function test_setValueIn_setMetricAssignmentReportsAssignmentFieldValue()
    {
        $this->metricAssignmentReport->expects($this->once())
                ->method("setAssignmentFieldValue")
                ->with($this->assignmentField, $this->assignmentFieldValueData);
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
