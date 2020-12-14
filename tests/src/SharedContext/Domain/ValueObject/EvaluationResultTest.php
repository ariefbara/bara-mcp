<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class EvaluationResultTest extends TestBase
{
    protected $evaluationResult;
    protected $status = "extend", $extendDays = 100;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationResult = new TestableEvaluationResult("fail", null);
    }
    
    protected function executeConstruct()
    {
        return new TestableEvaluationResult($this->status, $this->extendDays);
    }
    public function test_construct_setProperties()
    {
        $evaluationResult = $this->executeConstruct();
        $this->assertEquals($this->status, $evaluationResult->status);
        $this->assertEquals($this->extendDays, $evaluationResult->extendDays);
    }
    public function test_construct_invalidStatus_badRequest()
    {
        $this->status = "invalid";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: invalid evaluation status";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_failStatus_setEmptyExtendDays()
    {
        $this->status = "fail";
        $evaluationResult = $this->executeConstruct();
        $this->assertNull($evaluationResult->extendDays);
    }
    public function test_construct_passStatus_setEmptyExtendDays()
    {
        $this->status = "pass";
        $evaluationResult = $this->executeConstruct();
        $this->assertNull($evaluationResult->extendDays);
    }
    
    public function test_isFail_failStatus_returnTrue()
    {
        $this->assertTrue($this->evaluationResult->isFail());
    }
    public function test_isFail_nonFailStatus_returnFalse()
    {
        $this->evaluationResult->status = "pass";
        $this->assertFalse($this->evaluationResult->isFail());
    }
    
    public function test_isComplete_failStatus_returnTrue()
    {
        $this->assertTrue($this->evaluationResult->isCompleted());
    }
    public function test_isComplete_extendStatus_returnFalse()
    {
        $this->evaluationResult->status = "extend";
        $this->assertFalse($this->evaluationResult->isCompleted());
    }
    public function test_isComplete_passStatus_returnTrue()
    {
        $this->evaluationResult->status = "pass";
        $this->assertTrue($this->evaluationResult->isCompleted());
    }
}

class TestableEvaluationResult extends EvaluationResult
{
    public $status;
    public $extendDays;
}
