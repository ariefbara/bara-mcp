<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\Coordinator;
use Firm\Domain\Model\Firm\Program\EvaluationPlan;
use Firm\Domain\Model\Firm\Program\Participant;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\EvaluationResult;
use Tests\TestBase;

class EvaluationTest extends TestBase
{

    protected $participant;
    protected $evaluationPlan;
    protected $coordinator;
    protected $evaluation;
    protected $evaluationResult;
    protected $id = "newId", $fail = true;
    protected $status = "extend", $extendDays = 90;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $evaluationData = new EvaluationData("pass", null);
        $this->evaluation = new TestableEvaluation(
                $this->participant, "id", $this->evaluationPlan, $evaluationData, $this->coordinator);
        
        $this->evaluationResult = $this->buildMockOfClass(EvaluationResult::class);
        $this->evaluation->evaluationResult = $this->evaluationResult;
    }

    protected function getEvaluationData()
    {
        return new EvaluationData($this->status, $this->extendDays);
    }

    protected function executeConstruct()
    {
        return new TestableEvaluation(
                $this->participant, $this->id, $this->evaluationPlan, $this->getEvaluationData(), $this->coordinator);
    }
    public function test_construct_setProperties()
    {
        $evaluation = $this->executeConstruct();
        $this->assertEquals($this->participant, $evaluation->participant);
        $this->assertEquals($this->id, $evaluation->id);
        $this->assertEquals($this->evaluationPlan, $evaluation->evaluationPlan);
        $this->assertEquals($this->coordinator, $evaluation->coordinator);
        
        $evaluationResult = new EvaluationResult($this->status, $this->extendDays);
        $this->assertEquals($evaluationResult, $evaluation->evaluationResult);
        $submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->assertEquals($submitTime, $evaluation->submitTime);
    }
    public function test_construct_failResult_failParticipant()
    {
        $this->status = "fail";
        $this->participant->expects($this->once())
                ->method("fail");
        $this->executeConstruct();
    }
    
    protected function executeIsCompletedEvaluationForPlan()
    {
        $this->evaluationResult->expects($this->once())
                ->method("isCompleted")
                ->willReturn(true);
        return $this->evaluation->isCompletedEvaluationForPlan($this->evaluationPlan);
    }
    public function test_isCompletedEvaluationForPlan_completeResultOfSamePlan_returnTrue()
    {
        $this->assertTrue($this->executeIsCompletedEvaluationForPlan());
    }
    public function test_isCompletedEvaluationForPlan_incompleteResult_returnFalse()
    {
        $this->evaluationResult->expects($this->once())
                ->method("isCompleted")
                ->willReturn(false);
        $this->assertFalse($this->executeIsCompletedEvaluationForPlan());
    }
    public function test_isCompletedEvaluationForPlan_differentPlan_returnFalse()
    {
        $this->evaluation->evaluationPlan = $this->buildMockOfClass(EvaluationPlan::class);
        $this->assertFalse($this->executeIsCompletedEvaluationForPlan());
    }

}

class TestableEvaluation extends Evaluation
{

    public $participant;
    public $id;
    public $evaluationPlan;
    public $coordinator;
    public $evaluationResult;
    public $submitTime;

}
