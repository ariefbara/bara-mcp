<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Firm\Domain\ {
    Model\Firm\Program\Metric,
    Model\Firm\Program\Participant,
    Model\Firm\Program\Participant\MetricAssignment\AssignmentField,
    Service\MetricAssignmentDataProvider
};
use Resources\Domain\ValueObject\DateInterval;
use Tests\TestBase;

class MetricAssignmentTest extends TestBase
{
    protected $participant;
    protected $metricAssignment;
    protected $assignmentField;
    protected $id = "newId";
    protected $startDate;
    protected $endDate;
    protected $metric, $target = 999;
    protected $metricAssignmentDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $startDate = new DateTimeImmutable("-1 days");
        $endDate = new DateTimeImmutable("+1 days");
        $this->metricAssignmentDataProvider = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
        $metricAssignmentDataProvider = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
        $metricAssignmentDataProvider->expects($this->any())->method("getStartDate")->willReturn($startDate);
        $metricAssignmentDataProvider->expects($this->any())->method("getEndDate")->willReturn($endDate);
        
        $this->metricAssignment = new TestableMetricAssignment($this->participant, "id", $metricAssignmentDataProvider);
        $this->assignmentField = $this->buildMockOfClass(AssignmentField::class);
        $this->metricAssignment->assignmentFields->add($this->assignmentField);
        
        $this->startDate = new \DateTimeImmutable("+1 month");
        $this->endDate = new \DateTimeImmutable("+6 month");
        $this->metric = $this->buildMockOfClass(Metric::class);
        $this->metricAssignmentDataProvider = $this->buildMockOfClass(MetricAssignmentDataProvider::class);
        $this->metricAssignmentDataProvider->expects($this->any())
                ->method("iterateMetrics")
                ->willReturn([$this->metric]);
        $this->metricAssignmentDataProvider->expects($this->any())
                ->method("pullTargetCorrespondWithMetric")
                ->with($this->metric)
                ->willReturn($this->target);
    }
    
    protected function setMetricAssignmentGetDateMethod()
    {
        $this->metricAssignmentDataProvider->expects($this->any())->method("getStartDate")->willReturn($this->startDate);
        $this->metricAssignmentDataProvider->expects($this->any())->method("getEndDate")->willReturn($this->endDate);
    }
    
    protected function executeConstruct()
    {
        $this->participant->expects($this->any())
                ->method("belongsInTheSameProgramAs")
                ->willReturn(true);
        $this->setMetricAssignmentGetDateMethod();
        return new TestableMetricAssignment($this->participant, $this->id, $this->metricAssignmentDataProvider);
    }
    public function test_construct_setProperties()
    {
        $metricAssignemt = $this->executeConstruct();
        $this->assertEquals($this->participant, $metricAssignemt->participant);
        $this->assertEquals($this->id, $metricAssignemt->id);
        $startEndDate = new DateInterval($this->startDate, $this->endDate);
        $this->assertEquals($startEndDate, $metricAssignemt->startEndDate);
    }
    public function test_construct_emptyStartDate_badRequestError()
    {
        $this->startDate = null;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: metric assignment start date is mandatory";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_construct_emptyEndDate_badRequestError()
    {
        $this->endDate = null;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: metric assignment end date is mandatory";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_construct_aggregateAssignmentField()
    {
        $metricAssignment = $this->executeConstruct();
        $this->assertEquals(1, $metricAssignment->assignmentFields->count());
        $this->assertInstanceOf(AssignmentField::class, $metricAssignment->assignmentFields->first());
    }
    public function test_construct_metricFromProviderNotBelongsToSameProgramAsParticipant_forbiddenError()
    {
        $this->participant->expects($this->once())
                ->method("belongsInTheSameProgramAs")
                ->with($this->metric)
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden : unable to assign metric from other program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeUpdate()
    {
        $this->participant->expects($this->any())
                ->method("belongsInTheSameProgramAs")
                ->willReturn(true);
        $this->setMetricAssignmentGetDateMethod();
        $this->metricAssignment->update($this->metricAssignmentDataProvider);
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $startEndDate = new DateInterval($this->startDate, $this->endDate);
        $this->assertEquals($startEndDate, $this->metricAssignment->startEndDate);
    }
    public function test_update_updateAllAssignmentFields()
    {
        $this->assignmentField->expects($this->once())
                ->method("update")
                ->with($this->metricAssignmentDataProvider);
        $this->executeUpdate();
    }
    public function test_update_existingAssignmentFieldAlreadyRemoved_preventUpdate()
    {
        $this->assignmentField->expects($this->once())
                ->method("isRemoved")
                ->willReturn(true);
        $this->assignmentField->expects($this->never())
                ->method("update");
        $this->executeUpdate();
    }
    public function test_update_addLefoverMetricInProviderAsNewAssignmentField()
    {
        $this->executeUpdate();
        $this->assertEquals(2, $this->metricAssignment->assignmentFields->count());
        $this->assertInstanceOf(AssignmentField::class, $this->metricAssignment->assignmentFields->last());
    }
    
/*
    public function test_update_noDataCorrespondToAssignedMetric_removeAssignemnt()
    {
        $this->metricAssignmentDataProvider->expects($this->once())
                ->method("pullMetricAssignmentDataCorrespondWithMetric")
                ->with($this->metric)
                ->willReturn(null);
        $this->executeUpdate();
        $this->assertTrue($this->metricAssignment->removed);
    }
 * 
 */
    
}

class TestableMetricAssignment extends MetricAssignment
{
    public $participant;
    public $id;
    public $startEndDate;
    public $assignmentFields;
}
