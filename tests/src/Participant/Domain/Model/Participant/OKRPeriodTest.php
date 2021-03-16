<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResultData;
use Participant\Domain\Model\Participant\OKRPeriod\ObjectiveData;
use Resources\Domain\Data\DataCollection;
use Resources\Domain\ValueObject\DateInterval;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\TestBase;

class OKRPeriodTest extends TestBase
{
    protected $participant;
    protected $okrPeriod;
    protected $period;
    protected $approvalStatus;
    protected $objective;
    protected $id = 'newOKRPeriodId';
    protected $labelData;
    protected $startDate;
    protected $endDate;
    protected $objectiveData, $objectiveId = 'objectiveId';
    protected $reportDate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->labelData = $this->buildMockOfClass(LabelData::class);
        $this->labelData->expects($this->any())->method('getName')->willReturn('okr period name');
        $this->startDate = new DateTimeImmutable('+1 days');
        $this->endDate = new DateTimeImmutable('+10 days');
        $this->objectiveData = $this->buildMockOfClass(ObjectiveData::class);
        $this->objectiveData->expects($this->any())->method('getlabelData')->willReturn($this->labelData);
        $this->objectiveData->expects($this->any())->method('getWeight')->willReturn(30);
        $keyResultDataCollection = new DataCollection();
        $keyResultDataCollection->push(new KeyResultData($this->labelData, 10000, 20), null);
        $this->objectiveData->expects($this->any())->method('getKeyResultDataIterator')->willReturn($keyResultDataCollection);
        
        $this->okrPeriod = new TestableOKRPeriod($this->participant, 'id', $this->getOkrPeriodData());
        $this->okrPeriod->label = $this->buildMockOfClass(Label::class);
        $this->period = $this->buildMockOfClass(DateInterval::class);
        $this->okrPeriod->period = $this->period;
        $this->approvalStatus = $this->buildMockOfClass(OKRPeriodApprovalStatus::class);
        $this->okrPeriod->approvalStatus = $this->approvalStatus;
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->okrPeriod->objectives->clear();
        $this->okrPeriod->objectives->add($this->objective);
        $this->reportDate = new \DateTimeImmutable();
    }
    protected function getOkrPeriodData()
    {
        $okrPeriodData = new OKRPeriodData($this->labelData, $this->startDate, $this->endDate);
        $okrPeriodData->addObjectiveData($this->objectiveData, $this->objectiveId);
        return $okrPeriodData;
    }
    protected function getOkrPeriodDataWithEmptyObjective()
    {
        return new OKRPeriodData($this->labelData, $this->startDate, $this->endDate);
    }
    
    protected function executeConstruct()
    {
        return new TestableOKRPeriod($this->participant, $this->id, $this->getOkrPeriodData());
    }
    public function test_construct_setProperties()
    {
        $okrPeriod = $this->executeConstruct();
        $this->assertEquals($this->participant, $okrPeriod->participant);
        $this->assertEquals($this->id, $okrPeriod->id);
        $this->assertEquals(new Label($this->labelData), $okrPeriod->label);
        $this->assertEquals(new DateInterval($this->startDate, $this->endDate), $okrPeriod->period);
        $approvalStatus = new OKRPeriodApprovalStatus(OKRPeriodApprovalStatus::UNCONCLUDED);
        $this->assertEquals($approvalStatus, $okrPeriod->approvalStatus);
        $this->assertFalse($okrPeriod->cancelled);
        $this->assertInstanceOf(ArrayCollection::class, $okrPeriod->objectives);
    }
    public function test_construct_addObjectiveToCollection()
    {
        $okrPeriod = $this->executeConstruct();
        $this->assertInstanceOf(Objective::class, $okrPeriod->objectives->last());
    }
    public function test_construct_noObjectiveAggregated_forbidden()
    {
        $operation = function (){
            new TestableOKRPeriod($this->participant, $this->id, $this->getOkrPeriodDataWithEmptyObjective());
        };
        $errorDetail = 'forbidden: okr period must have at least one active objective';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    public function test_isManageableByParticipant_sameParticipant_returnTrue()
    {
        $this->assertTrue($this->okrPeriod->isManageableByParticipant($this->participant));
    }
    public function test_isManageableByParticipant_differentParticipant_returnFalse()
    {
        $this->assertFalse($this->okrPeriod->isManageableByParticipant($this->buildMockOfClass(Participant::class)));
    }
    
    protected function executeUpdate()
    {
        $this->okrPeriod->update($this->getOkrPeriodData());
    }
    public function test_update_changeProperties()
    {
        $this->executeUpdate();
        $this->assertEquals(new Label($this->labelData), $this->okrPeriod->label);
        $this->assertEquals(new DateInterval($this->startDate, $this->endDate), $this->okrPeriod->period);
    }
    public function test_update_updateAggregateExistingObjective()
    {
        $this->objective->expects($this->once())
                ->method('updateAggregate')
                ->with($this->getOkrPeriodData());
        $this->executeUpdate();
    }
    public function test_update_aggregateNewObjectiveNotExistInCollection()
    {
        $this->executeUpdate();
        $this->assertEquals(2, $this->okrPeriod->objectives->count());
        $this->assertInstanceOf(Objective::class, $this->okrPeriod->objectives->last());
    }
    public function test_update_alreadyCancelled_forbidden()
    {
        $this->okrPeriod->cancelled = true;
        $operation = function (){
            $this->executeUpdate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: okr period already cancelled');
    }
    public function test_update_noActiveObjectiveAfterUpdate_forbidden()
    {
        $this->objective->expects($this->once())
                ->method('isActive')
                ->willReturn(false);
        $operation = function (){
            $this->okrPeriod->update($this->getOkrPeriodDataWithEmptyObjective());
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: okr period must have at least one active objective');
    }
    public function test_update_alreadyConcluded_forbidden()
    {
        $this->approvalStatus->expects($this->once())
                ->method('isConcluded')
                ->willReturn(true);
        $operation = function (){
            $this->executeUpdate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: okr period already concluded');
    }
    
    protected function executeCancel()
    {
        $this->okrPeriod->cancel();
    }
    public function test_cancel_setCancelledTrue()
    {
        $this->executeCancel();
        $this->assertTrue($this->okrPeriod->cancelled);
    }
    public function test_cancel_alreadyCancelled_forbidden()
    {
        $this->okrPeriod->cancelled = true;
        $operation = function (){
            $this->executeCancel();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: okr period already cancelled');
    }
    public function test_cancel_disableAllObjectives()
    {
        $this->objective->expects($this->once())
                ->method('disable');
        $this->executeCancel();
    }
    public function test_cancel_alreadyConcluded_forbidden()
    {
        $this->approvalStatus->expects($this->once())
                ->method('isConcluded')
                ->willReturn(true);
        $operation = function (){
            $this->executeCancel();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: okr period already concluded');
    }
    
    protected function executeInConflictWith()
    {
        $this->period->expects($this->any())
                ->method('intersectWith')
                ->willReturn(true);
        $okrPeriod = $this->executeConstruct();
        return $this->okrPeriod->inConflictWith($okrPeriod);
    }
    public function test_inconflictWith_returnPeriodsIntersectWithResult()
    {
        $this->period->expects($this->once())
                ->method('intersectWith')
                ->with(new DateInterval($this->startDate, $this->endDate));
        $this->executeInConflictWith();
    }
    public function test_inConflictWith_alreadyCancelled_returnFalse()
    {
        $this->okrPeriod->cancelled = true;
        $this->assertFalse($this->executeInConflictWith());
    }
    public function test_inConflictWith_alredyRejected_returnFalse()
    {
        $this->approvalStatus->expects($this->once())
                ->method('isRejected')
                ->willReturn(true);
        $this->assertFalse($this->executeInConflictWith());
    }
    public function test_inConflictWith_sameEntity_returnFalse()
    {
        $this->period->expects($this->any())
                ->method('intersectWith')
                ->willReturn(true);
        $this->assertFalse($this->okrPeriod->inConflictWith($this->okrPeriod));
    }
    
    protected function executeCanAcceptReportAt()
    {
        $this->approvalStatus->expects($this->any())->method('isApproved')->willReturn(true);
        $this->period->expects($this->any())
                ->method('contain')
                ->willReturn(true);
        return $this->okrPeriod->canAcceptReportAt($this->reportDate);
    }
    public function test_canAcceptReportAt_returnPeriodsContainResult()
    {
        $this->period->expects($this->once())
                ->method('contain')
                ->with($this->reportDate);
        $this->executeCanAcceptReportAt();
    }
    public function test_canAcceptReportAt_cancelled_forbidden()
    {
        $this->okrPeriod->cancelled = true;
        $this->assertFalse($this->executeCanAcceptReportAt());
    }
    public function test_canAcceptReportAt_notApproved_forbidden()
    {
        $this->approvalStatus->expects($this->any())->method('isApproved')->willReturn(false);
        $this->assertFalse($this->executeCanAcceptReportAt());
    }
}

class TestableOKRPeriod extends OKRPeriod
{
    public $participant;
    public $id;
    public $label;
    public $period;
    public $cancelled;
    public $approvalStatus;
    public $objectives;
}
