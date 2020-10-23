<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Team,
    Model\Participant\MetricAssignment,
    Model\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class MetricAssignmentReportTest extends TestBase
{
    protected $metricAssignment;
    protected $metricAssignmentReport;
    protected $id = "newId";
    protected $observeTime;
    protected $assignmentFieldValue;
    protected $metricAssignmentReportData;
    protected $assignmentField, $value = 999.99;
    protected $team;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignment = $this->buildMockOfClass(MetricAssignment::class);
        $this->metricAssignmentReport = new TestableMetricAssignmentReport($this->metricAssignment, "id", new DateTimeImmutable());
        $this->observeTime = new DateTimeImmutable("-1 days");
        $this->assignmentFieldValue = $this->buildMockOfClass(AssignmentFieldValue::class);
        $this->metricAssignmentReport->assignmentFieldValues->add($this->assignmentFieldValue);
        
        $this->metricAssignmentReportData = $this->buildMockOfClass(MetricAssignmentReportData::class);
        
        $this->assignmentField = $this->buildMockOfClass(AssignmentField::class);
        
        $this->team = $this->buildMockOfClass(Team::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableMetricAssignmentReport($this->metricAssignment, $this->id, $this->observeTime);
    }
    public function test_construct_setProperties()
    {
        $metricAssignmentReport = $this->executeConstruct();
        $this->assertEquals($this->metricAssignment, $metricAssignmentReport->metricAssignment);
        $this->assertEquals($this->id, $metricAssignmentReport->id);
        $this->assertEquals($this->observeTime, $metricAssignmentReport->observeTime);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $metricAssignmentReport->submitTime);
        $this->assertFalse($metricAssignmentReport->removed);
        $this->assertInstanceOf(ArrayCollection::class, $metricAssignmentReport->assignmentFieldValues);
    }
    
    protected function executeUpdate()
    {
        $this->metricAssignmentReport->update($this->metricAssignmentReportData);
    }
    public function test_update_executeMetricAssignmentsSetActiveAssignmentFieldValuesToMethod()
    {
        $this->metricAssignment->expects($this->once())
                ->method("setActiveAssignmentFieldValuesTo")
                ->with($this->metricAssignmentReport, $this->metricAssignmentReportData);
        $this->executeUpdate();
    }
    public function test_update_removeAssignmentFieldValueCorrespondToObsoleteAssignmentField()
    {
        $this->assignmentFieldValue->expects($this->once())
                ->method("isNonRemovedAssignmentFieldValueCorrespondWithObsoleteAssignmentField")
                ->willReturn(true);
        $this->assignmentFieldValue->expects($this->once())
                ->method("remove");
        $this->executeUpdate();
    }
    
    protected function executeSetAssignmentFieldValue()
    {
        $this->metricAssignmentReport->setAssignmentFieldValue($this->assignmentField, $this->value);
    }
    public function test_setAssignmentFieldValue_addAssignmentFieldValueToCollection()
    {
        $this->executeSetAssignmentFieldValue();
        $this->assertEquals(2, $this->metricAssignmentReport->assignmentFieldValues->count());
        $this->assertInstanceOf(AssignmentFieldValue::class, $this->metricAssignmentReport->assignmentFieldValues->last());
    }
    public function test_setAssignmentFieldValue_hasNonRemovedAssignmentFieldValueCorrespondToSameAssignmentField_updateThisValue()
    {
        $this->assignmentFieldValue->expects($this->once())
                ->method("isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField")
                ->with($this->assignmentField)
                ->willReturn(true);
        $this->assignmentFieldValue->expects($this->once())
                ->method("update")
                ->with($this->value);
        $this->executeSetAssignmentFieldValue();
    }
    public function test_setAssignmentFieldValue_hasNonRemovedAssignmentFieldValueCorrespondToSameAssignmentField_preventAddNewFieldValue()
    {
        $this->assignmentFieldValue->expects($this->once())
                ->method("isNonRemovedAssignmentFieldValueCorrespondWithAssignmentField")
                ->with($this->assignmentField)
                ->willReturn(true);
        $this->executeSetAssignmentFieldValue();
        $this->assertEquals(1, $this->metricAssignmentReport->assignmentFieldValues->count());
    }
    
    public function test_belongsToTeam_returnMetricAssignmentBelongsToTeamResult()
    {
        $this->metricAssignment->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->team);
        $this->metricAssignmentReport->belongsToTeam($this->team);
    }
    
}

class TestableMetricAssignmentReport extends MetricAssignmentReport
{
    public $metricAssignment;
    public $id;
    public $observeTime;
    public $submitTime;
    public $removed;
    public $assignmentFieldValues;
}
