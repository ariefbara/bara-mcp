<?php

namespace Participant\Domain\Model\Participant\MetricAssignment;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\ {
    DependencyModel\Firm\Team,
    Model\Participant\MetricAssignment,
    Model\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue,
    Model\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValueData,
    Service\MetricAssignmentReportDataProvider
};
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class MetricAssignmentReportTest extends TestBase
{
    protected $metricAssignment;
    protected $metricAssignmentReport;
    protected $id = "newId";
    protected $observationTime;
    protected $assignmentFieldValue;
    protected $metricAssignmentReportDataProvider;
    protected $assignmentField, $assignmentFieldValueData;
    protected $team;

    protected function setUp(): void
    {
        parent::setUp();
        $this->metricAssignment = $this->buildMockOfClass(MetricAssignment::class);
        $this->metricAssignmentReport = new TestableMetricAssignmentReport($this->metricAssignment, "id", new DateTimeImmutable());
        $this->observationTime = new DateTimeImmutable("-1 days");
        $this->assignmentFieldValue = $this->buildMockOfClass(AssignmentFieldValue::class);
        $this->metricAssignmentReport->assignmentFieldValues->add($this->assignmentFieldValue);
        
        $this->metricAssignmentReportDataProvider = $this->buildMockOfClass(MetricAssignmentReportDataProvider::class);
        
        $this->assignmentField = $this->buildMockOfClass(AssignmentField::class);
        $this->assignmentFieldValueData = $this->buildMockOfClass(AssignmentFieldValueData::class);
        
        $this->team = $this->buildMockOfClass(Team::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableMetricAssignmentReport($this->metricAssignment, $this->id, $this->observationTime);
    }
    public function test_construct_setProperties()
    {
        $metricAssignmentReport = $this->executeConstruct();
        $this->assertEquals($this->metricAssignment, $metricAssignmentReport->metricAssignment);
        $this->assertEquals($this->id, $metricAssignmentReport->id);
        $this->assertEquals($this->observationTime, $metricAssignmentReport->observationTime);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $metricAssignmentReport->submitTime);
        $this->assertFalse($metricAssignmentReport->removed);
        $this->assertInstanceOf(ArrayCollection::class, $metricAssignmentReport->assignmentFieldValues);
    }
    
    protected function executeUpdate()
    {
        $this->metricAssignment->expects($this->any())
                ->method("isParticipantOwnAllAttachedFileInfo")
                ->willReturn(true);
        $this->metricAssignmentReport->update($this->metricAssignmentReportDataProvider);
    }
    public function test_update_executeMetricAssignmentsSetActiveAssignmentFieldValuesToMethod()
    {
        $this->metricAssignment->expects($this->once())
                ->method("setActiveAssignmentFieldValuesTo")
                ->with($this->metricAssignmentReport, $this->metricAssignmentReportDataProvider);
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
    public function test_update_metricAssignmentReportDataProviderContainUnmanageableFileInfo_forbidden()
    {
        $this->metricAssignment->expects($this->once())
                ->method("isParticipantOwnAllAttachedFileInfo")
                ->with($this->metricAssignmentReportDataProvider)
                ->willReturn(false);
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "forbidden: attached file info is unmanageable";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeSetAssignmentFieldValue()
    {
        $this->metricAssignmentReport->setAssignmentFieldValue($this->assignmentField, $this->assignmentFieldValueData);
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
                ->with($this->assignmentFieldValueData);
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
    public $observationTime;
    public $submitTime;
    public $removed;
    public $assignmentFieldValues;
}
