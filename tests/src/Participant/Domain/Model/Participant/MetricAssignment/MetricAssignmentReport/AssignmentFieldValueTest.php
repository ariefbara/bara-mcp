<?php

namespace Participant\Domain\Model\Participant\MetricAssignment\MetricAssignmentReport;

use Participant\Domain\Model\Participant\MetricAssignment\ {
    AssignmentField,
    MetricAssignmentReport
};
use Tests\TestBase;

class AssignmentFieldValueTest extends TestBase
{
    protected $metricAssignmentReport;
    protected $assignmentField;
    protected $assignmentFieldValue;
    protected $id = "newId", $value = 999.99;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignmentReport = $this->buildMockOfClass(MetricAssignmentReport::class);
        $this->assignmentField = $this->buildMockOfClass(AssignmentField::class);
        $this->assignmentFieldValue = new TestableAssignmentFieldValue($this->metricAssignmentReport, "id", $this->assignmentField, 111.11);
    }
    
    public function test_construct_setProperties()
    {
        $assignmentFieldValue = new TestableAssignmentFieldValue($this->metricAssignmentReport, $this->id, $this->assignmentField, $this->value);
        $this->assertEquals($this->metricAssignmentReport, $assignmentFieldValue->metricAssignmentReport);
        $this->assertEquals($this->id, $assignmentFieldValue->id);
        $this->assertEquals($this->assignmentField, $assignmentFieldValue->assignmentField);
        $this->assertEquals($this->value, $assignmentFieldValue->value);
        $this->assertFalse($assignmentFieldValue->removed);
    }
    
    public function test_update_changeValue()
    {
        $this->assignmentFieldValue->update($this->value);
        $this->assertEquals($this->value, $this->assignmentFieldValue->value);
    }
    
    public function test_remove_setRemovedStatusTrue()
    {
        $this->assignmentFieldValue->remove();
        $this->assertTrue($this->assignmentFieldValue->removed);
    }
    
    protected function executeIsNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField()
    {
        return $this->assignmentFieldValue->isNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField();
    }
    public function test_isNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField_returnFalse()
    {
        $this->assertFalse($this->executeIsNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField());
    }
    public function test_isNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField_aNonRemovedValueCorrespondToRemovedField_returnTrue()
    {
        $this->assignmentField->expects($this->once())->method("isRemoved")->willReturn(true);
        $this->assertTrue($this->executeIsNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField());
    }
    public function test_isNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField_alreadyRemoved_returnFalse()
    {
        $this->assignmentField->expects($this->any())->method("isRemoved")->willReturn(true);
        $this->assignmentFieldValue->removed = true;
        $this->assertFalse($this->executeIsNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField());
    }
    
    
    protected function executeIsNonRemovedAssignmentFieldValueCorrespondWithAssignmentField()
    {
        return $this->assignmentFieldValue->isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField($this->assignmentField);
    }
    public function test_isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField_returnTrue()
    {
        $this->assertTrue($this->executeIsNonRemovedAssignmentFieldValueCorrespondWithAssignmentField());
    }
    public function test_isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField_aNonRemovedValueCorrespondToDifferentField_returnFalse()
    {
        $this->assignmentFieldValue->assignmentField = $this->buildMockOfClass(AssignmentField::class);
        $this->assertFalse($this->executeIsNonRemovedAssignmentFieldValueCorrespondWithAssignmentField());
    }
    
    public function test_isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField_alreadyRemoved_returnFalse()
    {
        $this->assignmentFieldValue->removed = true;
        $this->assertFalse($this->executeIsNonRemovedAssignmentFieldValueCorrespondWithAssignmentField());
    }
    
}

class TestableAssignmentFieldValue extends AssignmentFieldValue
{
    public $metricAssignmentReport;
    public $id;
    public $assignmentField;
    public $value;
    public $removed;
}
