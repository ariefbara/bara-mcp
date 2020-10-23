<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use Tests\TestBase;

class MetricAssignmentReportDataTest extends TestBase
{
    protected $data;
    protected $assignmentFieldId = "assignmentFieldId", $value = 234;


    protected function setUp(): void
    {
        parent::setUp();
        $this->data = new TestableMetricAssignmentReportData();
    }
    
    public function test_addValueCorrespondWithAssignmentField_addAssignmentFieldIdAndValueMapToCollection()
    {
        $this->data->addValueCorrespondWithAssignmentField($this->assignmentFieldId, $this->value);
        $this->assertTrue(isset($this->data->collection[$this->assignmentFieldId]));
        $this->assertEquals($this->value, $this->data->collection[$this->assignmentFieldId]);
    }
    
    public function test_getValueCorrespondWithAssignmentField_returnValueMappedToSameFieldId()
    {
        $this->data->addValueCorrespondWithAssignmentField($this->assignmentFieldId, $this->value);
        $this->assertEquals($this->value, $this->data->getValueCorrespondWithAssignmentField($this->assignmentFieldId));
    }
    public function test_getValueCorrespondWithAssignmentField_noMapFound_returnNull()
    {
        $this->assertNull($this->data->getValueCorrespondWithAssignmentField($this->assignmentFieldId));
    }
}

class TestableMetricAssignmentReportData extends MetricAssignmentReportData
{
    public $collection;
}
