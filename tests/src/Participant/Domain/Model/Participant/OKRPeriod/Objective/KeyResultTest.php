<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective;

use Participant\Domain\Model\Participant\OKRPeriod\Objective;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport\KeyResultProgressReportData;
use Participant\Domain\Model\Participant\OKRPeriod\ObjectiveData;
use SharedContext\Domain\ValueObject\Label;
use SharedContext\Domain\ValueObject\LabelData;
use Tests\TestBase;

class KeyResultTest extends TestBase
{
    protected $objective;
    protected $id = 'newKeyResultId';
    protected $labelData;
    protected $target = 1000;
    protected $weight = 25;
    protected $keyResult;
    protected $objectiveData;
    protected $objectiveProgressReport, $objectiveProgressReportData, $keyResultProgressReportData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objective = $this->buildMockOfClass(Objective::class);
        $this->labelData = $this->buildMockOfClass(LabelData::class);
        $this->labelData->expects($this->any())->method('getName')->willReturn('new key result name');
        $keyResultData = new KeyResultData($this->labelData, '999', 9);
        $this->keyResult = new TestableKeyResult($this->objective, 'id', $keyResultData);
        $this->keyResult->label = null;
        
        $this->objectiveData = $this->buildMockOfClass(ObjectiveData::class);
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        $this->objectiveProgressReportData = $this->buildMockOfClass(ObjectiveProgressReportData::class);
        $this->keyResultProgressReportData = $this->buildMockOfClass(KeyResultProgressReportData::class);
    }
    protected function getKeyResultData()
    {
        return new KeyResultData($this->labelData, $this->target, $this->weight);
    }
    
    protected function executeConstruct()
    {
        return new TestableKeyResult($this->objective, $this->id, $this->getKeyResultData());
    }
    public function test_construct_setProperties()
    {
        $keyResult = new TestableKeyResult($this->objective, $this->id, $this->getKeyResultData());
        $this->assertEquals($this->objective, $keyResult->objective);
        $this->assertEquals($this->id, $keyResult->id);
        $this->assertEquals(new Label($this->labelData), $keyResult->label);
        $this->assertEquals($this->target, $keyResult->target);
        $this->assertEquals($this->weight, $keyResult->weight);
        $this->assertFalse($keyResult->disabled);
    }
    public function test_construct_emptyTarget_badRequest()
    {
        $this->target = 0;
        $operation = function (){
            $this->executeConstruct();
        };
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', "bad request: key result's target is mandatory");
    }
    public function test_construct_emptyWeight_badRequest()
    {
        $this->weight = 0;
        $operation = function (){
            $this->executeConstruct();
        };
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', "bad request: key result's weight is mandatory");
    }
    
    protected function executeUpdateAggregate()
    {
        $this->objectiveData->expects($this->any())
                ->method('pullKeyResultData')
                ->willReturn($this->getKeyResultData());
        $this->keyResult->updateAggregate($this->objectiveData);
    }
    public function test_updateAggregate_updateProperties()
    {
        $this->executeUpdateAggregate();
        $this->assertEquals(new Label($this->labelData), $this->keyResult->label);
        $this->assertEquals($this->target, $this->keyResult->target);
        $this->assertEquals($this->weight, $this->keyResult->weight);
    }
    public function test_update_emptyTarget_badRequest()
    {
        $this->target = 0;
        $operation = function (){
            $this->executeUpdateAggregate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', "bad request: key result's target is mandatory");
    }
    public function test_update_emptyWeight_badRequest()
    {
        $this->weight = 0;
        $operation = function (){
            $this->executeUpdateAggregate();
        };
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', "bad request: key result's weight is mandatory");
    }
    public function test_updateAggregate_nullKeyResultDataCorrespondWithId_disable()
    {
        $this->objectiveData->expects($this->any())
                ->method('pullKeyResultData')
                ->willReturn(null);
        $this->executeUpdateAggregate();
        $this->assertTrue($this->keyResult->disabled);
    }
    public function test_updateAggregate_alreadyDisabled_enable()
    {
        $this->keyResult->disabled = true;
        $this->executeUpdateAggregate();
        $this->assertFalse($this->keyResult->disabled);
    }
    
    public function test_disable_setDisabledTrue()
    {
        $this->keyResult->disable();
        $this->assertTrue($this->keyResult->disabled);
    }
    
    public function test_isActive_returnTrue()
    {
        $this->assertTrue($this->keyResult->isActive());
    }
    public function test_isActive_disabled_returnFalse()
    {
        $this->keyResult->disabled = true;
        $this->assertFalse($this->keyResult->isActive());
    }
    
    protected function executeSetProgressReportIn()
    {
        $this->objectiveProgressReportData->expects($this->any())
                ->method('pullKeyResultProgressReportData')
                ->with($this->keyResult->id)
                ->willReturn($this->keyResultProgressReportData);
        $this->keyResult->setProgressReportIn($this->objectiveProgressReport, $this->objectiveProgressReportData);
    }
    public function test_setProgressReport_setObjectiveProgressReportSetKeyResultProgressReport()
    {
        $this->objectiveProgressReport->expects($this->once())
                ->method('setKeyResultProgressReport')
                ->with($this->keyResult, $this->keyResultProgressReportData);
        $this->executeSetProgressReportIn();
    }
    public function test_setProgressReport_noKeyResultProgressReportDataCorrespondWithId_skipSettingKeyResultProgressReport()
    {
        $this->objectiveProgressReportData->expects($this->once())
                ->method('pullKeyResultProgressReportData')
                ->with($this->keyResult->id)
                ->willReturn(null);
        $this->objectiveProgressReport->expects($this->never())
                ->method('setKeyResultProgressReport');
        $this->executeSetProgressReportIn();
    }
    public function test_setProgressReport_inactiveKeyResult_skipSettingKeyResultProgressReport()
    {
        $this->keyResult->disabled = true;
        $this->objectiveProgressReport->expects($this->never())
                ->method('setKeyResultProgressReport');
        $this->executeSetProgressReportIn();
    }
}

class TestableKeyResult extends KeyResult
{
    public $objective;
    public $id;
    public $label;
    public $target;
    public $weight;
    public $disabled;
}
