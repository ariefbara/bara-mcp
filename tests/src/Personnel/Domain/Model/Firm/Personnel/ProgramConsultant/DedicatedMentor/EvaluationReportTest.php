<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;

use Config\EventList;
use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\DedicatedMentor;
use Personnel\Domain\Model\Firm\Program\EvaluationPlan;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class EvaluationReportTest extends TestBase
{
    protected $dedicatedMentor;
    protected $evaluationPlan;
    protected $id = 'new-id', $formRecordData;
    protected $evaluationReport, $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->evaluationReport = new TestableEvaluationReport(
                $this->dedicatedMentor, 'id', $this->evaluationPlan, $this->formRecordData);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->evaluationReport->formRecord = $this->formRecord;
        $this->evaluationReport->modifiedTime = new DateTimeImmutable('-1 weeks');
        $this->evaluationReport->recordedEvents = [];
    }
    
    protected function executeConstruct()
    {
        return new TestableEvaluationReport(
                $this->dedicatedMentor, $this->id, $this->evaluationPlan, $this->formRecordData);
    }
    public function test_construct_setProperties()
    {
        $evaluationReport = $this->executeConstruct();
        $this->assertEquals($this->dedicatedMentor, $evaluationReport->dedicatedMentor);
        $this->assertEquals($this->id, $evaluationReport->id);
        $this->assertEquals($this->evaluationPlan, $evaluationReport->evaluationPlan);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $evaluationReport->modifiedTime);
        $this->assertFalse($evaluationReport->cancelled);
    }
    public function test_construct_setFormRecordCreatedByEvaluationPlan()
    {
        $this->evaluationPlan->expects($this->once())
                ->method('createFormRecord')
                ->with($this->id, $this->formRecordData)
                ->willReturn($formRecord = $this->buildMockOfClass(FormRecord::class));
        $evaluationReport = $this->executeConstruct();
        $this->assertEquals($formRecord, $evaluationReport->formRecord);
    }
    public function test_construct_recordCommonEntityCreatedEvent()
    {
        $evaluationReport = $this->executeConstruct();
        $event = new CommonEvent(EventList::COMMON_ENTITY_CREATED, $this->id);
        $this->assertEquals($event, $evaluationReport->recordedEvents[0]);
    }
    
    public function test_assertManageableByDedicatedMentor_sameDedicatedMentor_void()
    {
        $this->evaluationReport->assertManageableByDedicatedMentor($this->dedicatedMentor);
        $this->markAsSuccess();
    }
    public function test_assertManageableByDedicatedMentor_differentDedicatedMentor_forbidden()
    {
        $this->assertRegularExceptionThrowed(function (){
            $dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
            $this->evaluationReport->assertManageableByDedicatedMentor($dedicatedMentor);
        }, 'Forbidden', 'forbidden: evaluation report is not manageable');
    }
    
    protected function executeUpdate()
    {
        $this->evaluationReport->update($this->formRecordData);
    }
    public function test_update_updateFormRecordAndModifiedTime()
    {
        $this->formRecord->expects($this->once())
                ->method('update')
                ->with($this->formRecordData);
        $this->executeUpdate();
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->evaluationReport->modifiedTime);
    }
    public function test_update_inactiveReport_forbidden()
    {
        $this->evaluationReport->cancelled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeUpdate();
        }, 'Forbidden', 'forbidden: evaluation report already cancelled');
    }
    public function test_update_recordCommonEntityCreatedEvent()
    {
        $this->executeUpdate();
        $event = new CommonEvent(EventList::COMMON_ENTITY_CREATED, $this->evaluationReport->id);
        $this->assertEquals($event, $this->evaluationReport->recordedEvents[0]);
    }
    
    protected function executeCancel()
    {
        $this->evaluationReport->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->executeCancel();
        $this->assertTrue($this->evaluationReport->cancelled);
    }
    public function test_cancel_inactiveReport()
    {
        $this->evaluationReport->cancelled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeCancel();
        }, 'Forbidden', 'forbidden: evaluation report already cancelled');
    }
    
    public function test_isActiveReportCorrespondWithEvaluationPlan_activeReportCorrespondToSamePlan_returnTrue()
    {
        $this->assertTrue($this->evaluationReport->isActiveReportCorrespondWithEvaluationPlan($this->evaluationPlan));
    }
    public function test_isActiveReportCorrespondWithEvaluationPlan_cancelledReport_returnFalse()
    {
        $this->evaluationReport->cancelled = true;
        $this->assertFalse($this->evaluationReport->isActiveReportCorrespondWithEvaluationPlan($this->evaluationPlan));
    }
    public function test_isActiveReportCorrespondWithEvaluationPlan_differenteEvaluationPlan_returnFalse()
    {
        $evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->assertFalse($this->evaluationReport->isActiveReportCorrespondWithEvaluationPlan($evaluationPlan));
    }
}

class TestableEvaluationReport extends EvaluationReport
{
    public $dedicatedMentor;
    public $id;
    public $evaluationPlan;
    public $formRecord;
    public $modifiedTime;
    public $cancelled;
    public $recordedEvents;
}
