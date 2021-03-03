<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective;

use DateInterval;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReportData;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\TestBase;

class ObjectiveProgressReportTest extends TestBase
{
    protected $objective;
    protected $id = 'newObjectiveProgressReportId';
    protected $reportDate;
    protected $objectiveProgressReport;
    protected $approvalStatus;
    protected $keyResultProgressReport;
    protected $keyResult, $keyResultProgressReportData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->reportDate = new DateTimeImmutable('-7 days');
        
        $objective = $this->buildMockOfClass(Objective::class);
        $objective->expects($this->any())->method('canAcceptReportAt')->willReturn(true);
        
        $this->objectiveProgressReport = new TestableObjectiveProgressReport($objective, 'id', $this->getObjectiveProgressReportData());
        $this->objectiveProgressReport->objective = $this->objective;
        
        $this->objectiveProgressReport->reportDate = new DateTimeImmutable();
        
        $this->approvalStatus = $this->buildMockOfClass(OKRPeriodApprovalStatus::class);
        $this->objectiveProgressReport->approvalStatus = $this->approvalStatus;
        
        $this->keyResultProgressReport = $this->buildMockOfClass(KeyResultProgressReport::class);
        $this->objectiveProgressReport->keyResultProgressReports->add($this->keyResultProgressReport);
        
        $this->keyResult = $this->buildMockOfClass(KeyResult::class);
        $this->keyResultProgressReportData = $this->buildMockOfClass(KeyResultProgressReportData::class);
    }
    protected function getObjectiveProgressReportData()
    {
        return new ObjectiveProgressReportData($this->reportDate);
    }
    
    public function test_isManageableByParticipant_returnObjectivesIsManageableByParticipantResult()
    {
        $this->objective->expects($this->once())
                ->method('isManageableByParticipant')
                ->with($participant = $this->buildMockOfClass(Participant::class));
        $this->objectiveProgressReport->isManageableByParticipant($participant);
    }
    
    protected function executeConstruct()
    {
        $this->objective->expects($this->any())
                ->method('canAcceptReportAt')
                ->willReturn(true);
        return new TestableObjectiveProgressReport($this->objective, $this->id, $this->getObjectiveProgressReportData());
    }
    public function test_construct_setProperties()
    {
        $report = $this->executeConstruct();
        $this->assertEquals($this->objective, $report->objective);
        $this->assertEquals($this->id, $report->id);
        $this->assertEquals($this->reportDate, $report->reportDate);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $report->submitTime);
        $this->assertEquals(new OKRPeriodApprovalStatus(OKRPeriodApprovalStatus::UNCONCLUDED), $report->approvalStatus);
        $this->assertfalse($report->cancelled);
        $this->assertInstanceOf(ArrayCollection::class, $report->keyResultProgressReports);
    }
    public function test_construct_reportDateNotAcceptableInObjective_forbidden()
    {
        $this->objective->expects($this->once())
                ->method('canAcceptReportAt')
                ->with($this->reportDate)
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: report date outside of okr period time');
    }
    public function test_construct_objectiveContainCoflictedObjectiveProgressReport_conflict()
    {
        $this->objective->expects($this->once())
                ->method('containProgressReportInConflictWith')
                ->willReturn(true);
        $operation = function (){
            $this->executeConstruct();
        };
        $this->assertRegularExceptionThrowed($operation, 'Conflict', 'conflict: this request cause conflict with other objective progress report');
    }
    public function test_construct_executeObjectivesAggregateKeyResultProgressReportTo()
    {
        $this->objective->expects($this->once())
                ->method('aggregateKeyResultProgressReportTo')
                ->with($this->anything(), $this->getObjectiveProgressReportData());
        $this->executeConstruct();
    }
    public function test_construct_reportDateInFuture_forbidden()
    {
        $this->reportDate = new \DateTimeImmutable('+1 days');
        $operation = function (){
            $this->executeConstruct();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: max progress report date is current date');
    }
    public function test_construct_reportDateInFutureButInSameDay()
    {
        $this->reportDate = new \DateTimeImmutable('+10 seconds');
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    
    protected function executeUpdate()
    {
        $this->objective->expects($this->any())->method('canAcceptReportAt')->willReturn(true);
        $this->objectiveProgressReport->update($this->getObjectiveProgressReportData());
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->reportDate, $this->objectiveProgressReport->reportDate);
    }
    public function test_update_objectiveRefuseReportAtReportDate()
    {
        $this->objective->expects($this->any())
                ->method('canAcceptReportAt')
                ->with($this->reportDate)
                ->willReturn(false);
        $operation = function (){
            $this->executeUpdate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: report date outside of okr period time');
    }
    public function test_update_objectiveContainCoflictedObjectiveProgressReport_conflict()
    {
        $this->objective->expects($this->once())
                ->method('containProgressReportInConflictWith')
                ->willReturn(true);
        $operation = function (){
            $this->executeUpdate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Conflict', 'conflict: this request cause conflict with other objective progress report');
    }
    public function test_update_executeObjectivesAggregateKeyResultProgressReportTo()
    {
        $this->objective->expects($this->once())
                ->method('aggregateKeyResultProgressReportTo')
                ->with($this->objectiveProgressReport, $this->getObjectiveProgressReportData());
        $this->executeUpdate();
    }
    public function test_update_disableAllKeyResultProgressReportCorrespondToInactiveKeyResult()
    {
        $this->keyResultProgressReport->expects($this->once())
                ->method('disableIfCorrespondWithInactiveKeyResult');
        $this->executeUpdate();
    }
    public function test_update_alreadyCancelled_forbidden()
    {
        $this->objectiveProgressReport->cancelled = true;
        $operation = function (){
            $this->executeUpdate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: inactive progress report');
    }
    public function test_update_alreadyConcluded_forbidden()
    {
        $this->approvalStatus->expects($this->once())->method('isConcluded')->willReturn(true);
        $operation = function (){
            $this->executeUpdate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: progress report already concluded');
    }
    
    protected function executeCancel()
    {
        $this->objectiveProgressReport->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->executeCancel();
        $this->assertTrue($this->objectiveProgressReport->cancelled);
    }
    public function test_cancel_alreadyConcluded_forbidden()
    {
        $this->approvalStatus->expects($this->once())->method('isConcluded')->willReturn(true);
        $operation = function (){
            $this->executeCancel();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: progress report already concluded');
    }
    public function test_cancel_alreadyCancelled_forbidden()
    {
        $this->objectiveProgressReport->cancelled = true;
        $operation = function (){
            $this->executeCancel();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: inactive progress report');
    }
    
    protected function executeInConflictWith()
    {
        $other = $this->executeConstruct();
        return $this->objectiveProgressReport->inConflictWith($other);
    }
    public function test_inConflictWith_differentReportDate_returnFalse()
    {
        $this->assertFalse($this->executeInConflictWith());
    }
    public function test_inConflictWith_sameAsOtherReportDate_returnTrue()
    {
        $this->objectiveProgressReport->reportDate = $this->reportDate;
        $this->assertTrue($this->executeInConflictWith());
    }
    public function test_inConflictWith_sameAsOtherReportDateButDifferentTime_returnTrue()
    {
        $date = $this->reportDate->add(new DateInterval('PT10S'));
        $this->objectiveProgressReport->reportDate = $date;
        $this->assertTrue($this->executeInConflictWith());
    }
    public function test_inConflictWith_alreadyCancelled_returnFalse()
    {
        $this->objectiveProgressReport->reportDate = $this->reportDate;
        $this->objectiveProgressReport->cancelled = true;
        $this->assertFalse($this->executeInConflictWith());
    }
    public function test_inConflictWith_alreadyConcluded_returnFalse()
    {
        $this->objectiveProgressReport->reportDate = $this->reportDate;
        $this->approvalStatus->expects($this->any())->method('isRejected')->willReturn(true);
        $this->assertFalse($this->executeInConflictWith());
    }
    public function test_inConflictWith_sameObjectiveProgressReport_returnFalse()
    {
        $this->assertFalse($this->objectiveProgressReport->inConflictWith($this->objectiveProgressReport));
    }
    
    protected function executeSetKeyResultProgressReport()
    {
        $this->objectiveProgressReport->setKeyResultProgressReport($this->keyResult, $this->keyResultProgressReportData);
    }
    public function test_setKeyResultProgressReport_addKeyResultProgressReportToCollection()
    {
        $this->executeSetKeyResultProgressReport();
        $this->assertEquals(2, $this->objectiveProgressReport->keyResultProgressReports->count());
        $this->assertInstanceOf(KeyResultProgressReport::class, $this->objectiveProgressReport->keyResultProgressReports->last());
    }
    public function test_setKeyResultProgressReport_containKeyResultProgressReportCorrespondToSameKeyResult_updateKeyResultProgressReport()
    {
        $this->keyResultProgressReport->expects($this->once())
                ->method('correspondWith')
                ->with($this->keyResult)
                ->willReturn(true);
        $this->keyResultProgressReport->expects($this->once())
                ->method('update')
                ->with($this->keyResultProgressReportData);
        $this->executeSetKeyResultProgressReport();
    }
    public function test_setKeyResultProgressReport_containKeyResultProgressReportCorrespondToSameKeyResult_preventAddNeKeyResultProgressReport()
    {
        $this->keyResultProgressReport->expects($this->once())
                ->method('correspondWith')
                ->with($this->keyResult)
                ->willReturn(true);
        $this->executeSetKeyResultProgressReport();
        $this->assertEquals(1, $this->objectiveProgressReport->keyResultProgressReports->count());
    }
}

class TestableObjectiveProgressReport extends ObjectiveProgressReport
{
    public $objective;
    public $id;
    public $reportDate;
    public $submitTime;
    public $approvalStatus;
    public $cancelled;
    public $keyResultProgressReports;
}
