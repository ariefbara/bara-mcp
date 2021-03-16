<?php

namespace Participant\Domain\Model\Participant\OKRPeriod;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\OKRPeriod;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResult;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResultData;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReportData;
use Participant\Domain\Model\Participant\OKRPeriodData;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class ObjectiveTest extends TestBase
{
    protected $okrPeriod;
    protected $id = 'newObjectiveId';
    protected $labelData;
    protected $weight = 30;
    protected $keyResultData, $keyResultId = 'keyResultId';
    protected $objective;
    protected $keyResult;
    protected $okrPeriodData;
    protected $participant;

    protected $objectiveProgressReport;
    protected $otherObjectiveProgressReport;
    protected $objectiveProgressReportId = 'objectiveProgressReportId', $objectiveProgressReportData, $reportDate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->okrPeriod = $this->buildMockOfClass(OKRPeriod::class);
        $this->labelData = $this->buildMockOfClass(LabelData::class);
        $this->labelData->expects($this->any())->method('getName')->willReturn('objective name');
        $this->keyResultData = $this->buildMockOfClass(KeyResultData::class);
        $this->keyResultData->expects($this->any())->method('getLabelData')->willReturn($this->labelData);
        $this->keyResultData->expects($this->any())->method('getTarget')->willReturn(1000);
        $this->keyResultData->expects($this->any())->method('getWeight')->willReturn(10);
        
        $this->objective = new TestableObjective($this->okrPeriod, 'id', $this->getObjectiveData());
        $this->objective->label = null;
        
        $this->keyResult = $this->buildMockOfClass(KeyResult::class);
        $this->objective->keyResults->clear();
        $this->objective->keyResults->add($this->keyResult);
        
        $this->okrPeriodData = $this->buildMockOfClass(OKRPeriodData::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        $this->otherObjectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        $this->objective->objectiveProgressReports = new ArrayCollection();
        $this->objective->objectiveProgressReports->add($this->objectiveProgressReport);
        
        $this->reportDate = new DateTimeImmutable();
        $this->objectiveProgressReportData = $this->buildMockOfClass(ObjectiveProgressReportData::class);
        $this->objectiveProgressReportData->expects($this->any())->method('getReportDate')->willReturn($this->reportDate);
        
    }
    protected function getObjectiveData()
    {
        $objectiveData = new ObjectiveData($this->labelData, $this->weight);
        $objectiveData->addKeyResultData($this->keyResultData, $this->keyResultId);
        return $objectiveData;
    }
    protected function getObjectiveDataWithoutKeyResult()
    {
        return new ObjectiveData($this->labelData, $this->weight);
    }
    
    public function test_isManageableByParticipant_returnOKRPeriodIsManageableByParticipantResult()
    {
        $this->okrPeriod->expects($this->once())
                ->method('isManageableByParticipant')
                ->with($this->participant);
        $this->objective->isManageableByParticipant($this->participant);
    }
    
    protected function executeConstruct()
    {
        return new TestableObjective($this->okrPeriod, $this->id, $this->getObjectiveData());
    }
    public function test_construct_setProperties()
    {
        $objective = $this->executeConstruct();
        $this->assertEquals($this->okrPeriod, $objective->okrPeriod);
        $this->assertEquals($this->id, $objective->id);
        $this->assertEquals(new Label($this->labelData), $objective->label);
        $this->assertEquals($this->weight, $objective->weight);
        $this->assertFalse($objective->disabled);
        $this->assertInstanceOf(ArrayCollection::class, $objective->keyResults);
    }
    public function test_construct_aggregateKeyResults()
    {
        $objective = $this->executeConstruct();
        $this->assertEquals(1, $objective->keyResults->count());
        $this->assertInstanceOf(KeyResult::class, $objective->keyResults->last());
    }
    public function test_construct_emptyWeight_badRequest()
    {
        $this->weight = 0;
        $operation = function (){
            $this->executeConstruct();
        };
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', "bad request: objective's weight is mandatory");
    }
    public function test_construct_noKeyResultAggregated_forbidden()
    {
        $operation = function (){
            new TestableObjective($this->okrPeriod, $this->id, $this->getObjectiveDataWithoutKeyResult());
        };
        $errorDetail = 'forbidden: objective must have at least one key result';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    public function test_isActive_disabledFalse_returnTrue()
    {
        $this->assertTrue($this->objective->isActive());
    }
    public function test_isActive_disabled_returnFalse()
    {
        $this->objective->disabled = true;
        $this->assertFalse($this->objective->isActive());
    }
    
    protected function executeUpdateAggregate()
    {
        $this->okrPeriodData->expects($this->any())
                ->method('pullObjectiveDataWithId')
                ->willReturn($this->getObjectiveData());
        $this->objective->updateAggregate($this->okrPeriodData);
    }
    public function test_update_updateProperties()
    {
        $this->okrPeriodData->expects($this->once())
                ->method('pullObjectiveDataWithId')
                ->with($this->objective->id)
                ->willReturn($this->getObjectiveData());
        $this->executeUpdateAggregate();
        $this->assertEquals(new Label($this->labelData), $this->objective->label);
        $this->assertEquals($this->weight, $this->objective->weight);
    }
    public function test_update_updateExistingKeyResult()
    {
        $this->keyResult->expects($this->once())
                ->method('updateAggregate')
                ->with($this->getObjectiveData());
        $this->executeUpdateAggregate();
    }
    public function test_update_aggregateKeyResults()
    {
        $this->executeUpdateAggregate();
        $this->assertEquals(2, $this->objective->keyResults->count());
        $this->assertInstanceOf(KeyResult::class, $this->objective->keyResults->last());
    }
    public function test_update_noActiveKeyResultExist_forbidden()
    {
        $this->keyResult->expects($this->once())
                ->method('isActive')
                ->willReturn(false);
        $this->okrPeriodData->expects($this->once())
                ->method('pullObjectiveDataWithId')
                ->with($this->objective->id)
                ->willReturn($this->getObjectiveDataWithoutKeyResult());
        $operation = function (){
            $this->executeUpdateAggregate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', "forbidden: objective must have at least one key result");
    }
    public function test_update_noObjectiveDataCorrespondWithId_disableObjective()
    {
        $this->okrPeriodData->expects($this->once())
                ->method('pullObjectiveDataWithId')
                ->with($this->objective->id)
                ->willReturn(null);
        $this->executeUpdateAggregate();
        $this->assertTrue($this->objective->disabled);
    }
    public function test_update_noObjectiveData_aggregateDisableKeyResults()
    {
        $this->keyResult->expects($this->once())
                ->method('disable');
        $this->okrPeriodData->expects($this->once())
                ->method('pullObjectiveDataWithId')
                ->with($this->objective->id)
                ->willReturn(null);
        $this->executeUpdateAggregate();
    }
    public function test_updateAggreagate_alreadyDisable_enable()
    {
        $this->objective->disabled = true;
        $this->executeUpdateAggregate();
        $this->assertFalse($this->objective->disabled);
    }
    
    protected function executeDisable()
    {
        $this->objective->disable();
    }
    public function test_disable_setDisabledTrue()
    {
        $this->executeDisable();
        $this->assertTrue($this->objective->disabled);
    }
    public function test_disable_aggreagetKeyResultsDisable()
    {
        $this->keyResult->expects($this->once())
                ->method('disable');
        $this->executeDisable();
    }
    
    protected function executeSubmitReport()
    {
        $this->okrPeriod->expects($this->once())
                ->method('canAcceptReportAt')
                ->willReturn(true);
        return $this->objective->submitReport($this->objectiveProgressReportId, $this->objectiveProgressReportData);
    }
    public function test_submitReport_returnObjectiveProgressReport()
    {
        $this->assertInstanceOf(ObjectiveProgressReport::class, $this->executeSubmitReport());
    }
    
    protected function executeCanAcceptReport()
    {
        $this->okrPeriod->expects($this->any())
                ->method('canAcceptReportAt')
                ->with($reportDate = new DateTimeImmutable())
                ->willReturn(true);
        return $this->objective->canAcceptReportAt($reportDate);
    }
    public function test_canAcceptReportAt_returnOKRPeriodCanAcceptReportAtResult()
    {
        $this->okrPeriod->expects($this->once())
                ->method('canAcceptReportAt');
        $this->executeCanAcceptReport();
    }
    public function test_canAcceptReportAt_disabled_returnFalse()
    {
        $this->objective->disabled = true;
        $this->assertFalse($this->executeCanAcceptReport());
    }
    
    protected function executeAggregateKeyResultProgressReportTo()
    {
        $this->keyResult->expects($this->any())
                ->method('isActive')
                ->willReturn(true);
        $this->objective->aggregateKeyResultProgressReportTo($this->objectiveProgressReport, $this->objectiveProgressReportData);
    }
    public function test_aggregateKeyResultProgressReportTo_executeAllKeyResultsSetProgressReportInMethod()
    {
        $this->keyResult->expects($this->once())
                ->method('setProgressReportIn')
                ->with($this->objectiveProgressReport, $this->objectiveProgressReportData);
        $this->executeAggregateKeyResultProgressReportTo();
    }
    
    protected function executeContainProgressReportInConflictWith()
    {
        return $this->objective->containProgressReportInConflictWith($this->otherObjectiveProgressReport);
    }
    public function test_containProgressReportInConflictWith_noConflictedProgressReportInCollection_returnFalse()
    {
        $this->assertFalse($this->executeContainProgressReportInConflictWith());
    }
    public function test_containProgressReportInConflictWith_containConflictedProgressReportInCollection_returnTrue()
    {
        $this->objectiveProgressReport->expects($this->any())
                ->method('inConflictWith')
                ->with($this->otherObjectiveProgressReport)
                ->willReturn(true);
        $this->assertTrue($this->executeContainProgressReportInConflictWith());
    }
}

class TestableObjective extends Objective
{
    public $okrPeriod;
    public $id;
    public $label;
    public $weight;
    public $disabled;
    public $keyResults;
    public $objectiveProgressReports;
}
