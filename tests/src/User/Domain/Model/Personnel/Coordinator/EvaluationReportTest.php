<?php

namespace User\Domain\Model\Personnel\Coordinator;

use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;
use User\Domain\ {
    DependencyModel\Firm\Program\EvaluationPlan,
    DependencyModel\Firm\Program\Participant,
    Model\Personnel\Coordinator
};

class EvaluationReportTest extends TestBase
{
    protected $coordinator;
    protected $participant;
    protected $evaluationPlan;
    protected $formRecordData;
    protected $evaluationReport;
    protected $id = "newId";
    protected $formRecord;


    protected function setUp(): void
    {
        parent::setUp();
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->evaluationReport = new TestableEvaluationReport($this->coordinator, "id", $this->participant, $this->evaluationPlan, $this->formRecordData);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->evaluationReport->formRecord = $this->formRecord;
    }
    
    public function test_construct_setProperties()
    {
        $evaluationReport = new TestableEvaluationReport(
                $this->coordinator, $this->id, $this->participant, $this->evaluationPlan, $this->formRecordData);
        $this->assertEquals($this->coordinator, $evaluationReport->coordinator);
        $this->assertEquals($this->id, $evaluationReport->id);
        $this->assertEquals($this->participant, $evaluationReport->participant);
        $this->assertEquals($this->evaluationPlan, $evaluationReport->evaluationPlan);
        $this->assertInstanceOf(FormRecord::class, $evaluationReport->formRecord);
    }
    
    public function test_update_updateFormRecord()
    {
        $this->formRecord->expects($this->once())
                ->method("update")
                ->with($this->formRecordData);
        $this->evaluationReport->update($this->formRecordData);
    }
    
    public function test_aReportOfEvaluationPlanCorrespondWithParticipant_sameParticipantAndEvaluationPlan_returnTrue()
    {
        $this->assertTrue($this->evaluationReport->aReportOfEvaluationPlanCorrespondWithParticipant($this->participant, $this->evaluationPlan));
    }
    public function test_aReportOfEvaluationPlanCorrespondWithParticipant_differentParticipant_returnFalse()
    {
        $participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->evaluationReport->aReportOfEvaluationPlanCorrespondWithParticipant($participant, $this->evaluationPlan));
    }
    public function test_aReportOfEvaluationPlanCorrespondWithParticipant_differentEvaluationPlan_returnFalse()
    {
        $evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->assertFalse($this->evaluationReport->aReportOfEvaluationPlanCorrespondWithParticipant($this->participant, $evaluationPlan));
    }
}

class TestableEvaluationReport extends EvaluationReport
{
    public $coordinator;
    public $id;
    public $participant;
    public $evaluationPlan;
    public $formRecord;
}
