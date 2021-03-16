<?php

namespace Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;

use Participant\Domain\Model\Participant\OKRPeriod\Objective\KeyResult;
use Participant\Domain\Model\Participant\OKRPeriod\Objective\ObjectiveProgressReport;
use Tests\TestBase;

class KeyResultProgressReportTest extends TestBase
{
    protected $objectiveProgressReport;
    protected $keyResult;
    protected $keyResultProgressReport;
    protected $id = 'newId';
    protected $value = 99;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->objectiveProgressReport = $this->buildMockOfClass(ObjectiveProgressReport::class);
        $this->keyResult = $this->buildMockOfClass(KeyResult::class);
        $keyResultProgressReportData = new KeyResultProgressReportData(11);
        $this->keyResultProgressReport = new TestableKeyResultProgressReport(
                $this->objectiveProgressReport, $this->keyResult, 'id', $keyResultProgressReportData);
    }
    protected function getKeyResultProgressReportData()
    {
        return new KeyResultProgressReportData($this->value);
    }
    
    protected function executeConstruct()
    {
        return new TestableKeyResultProgressReport(
                $this->objectiveProgressReport, $this->keyResult, $this->id, $this->getKeyResultProgressReportData());
    }
    public function test_construct_setProperties()
    {
        $keyResultProgressReport = $this->executeConstruct();
        $this->assertEquals($this->objectiveProgressReport, $keyResultProgressReport->objectiveProgressReport);
        $this->assertEquals($this->keyResult, $keyResultProgressReport->keyResult);
        $this->assertEquals($this->id, $keyResultProgressReport->id);
        $this->assertEquals($this->value, $keyResultProgressReport->value);
        $this->assertFalse($keyResultProgressReport->disabled);
    }
    
    protected function executeUpdate()
    {
        $this->keyResultProgressReport->update($this->getKeyResultProgressReportData());
    }
    public function test_update_udpateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->value, $this->keyResultProgressReport->value);
    }
    public function test_update_disabled_setEnable()
    {
        $this->keyResultProgressReport->disabled = true;
        $this->executeUpdate();
        $this->assertFalse($this->keyResultProgressReport->disabled);
    }
    
    protected function executeDisableIfCorrespondWithInactiveKeyResult()
    {
        $this->keyResultProgressReport->disableIfCorrespondWithInactiveKeyResult();
    }
    public function test_disableIfCorrespondWithInactiveKeyResult_setDisabled()
    {
        $this->executeDisableIfCorrespondWithInactiveKeyResult();
        $this->assertTrue($this->keyResultProgressReport->disabled);
    }
    public function test_disableIfCorrespondWithInactiveKeyResult_activeKeyresult_NOP()
    {
        $this->keyResult->expects($this->any())->method('isActive')->willReturn(true);
        $this->executeDisableIfCorrespondWithInactiveKeyResult();
        $this->assertFalse($this->keyResultProgressReport->disabled);
    }
    
    public function test_correspondWith_sameKeyResult_returnTrue()
    {
        $this->assertTrue($this->keyResultProgressReport->correspondWith($this->keyResult));
    }
    public function test_correspondWith_differentKeyResult_returnFalse()
    {
        $this->assertFalse($this->keyResultProgressReport->correspondWith($this->buildMockOfClass(KeyResult::class)));
    }
}

class TestableKeyResultProgressReport extends KeyResultProgressReport
{
    public $objectiveProgressReport;
    public $keyResult;
    public $id;
    public $value;
    public $disabled;
}
