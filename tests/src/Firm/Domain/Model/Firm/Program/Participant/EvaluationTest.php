<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\ {
    Coordinator,
    EvaluationPlan,
    Participant
};
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\ValueObject\EvaluationResult;
use Tests\TestBase;

class EvaluationTest extends TestBase
{

    protected $participant;
    protected $evaluationPlan;
    protected $coordinator;
    protected $evaluation;
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
    public function test_construct_failResult_disableParticipant()
    {
        $this->status = "fail";
        $this->participant->expects($this->once())
                ->method("disable");
        $this->executeConstruct();
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
