<?php
namespace Resources\Domain\ValueObject;

use Tests\TestBase;

class IntegerRangeTest extends TestBase
{
    protected $minValue = 1, $maxValue = 99;
    protected $vo;
    
    protected function setUp(): void {
        parent::setUp();
        $this->vo = new TestableIntegerRange($this->minValue, $this->maxValue);
    }
    
    private function executeConstruct() {
        return new TestableIntegerRange($this->minValue, $this->maxValue);
    }
    function test_construct_createIntegerRangeVO() {
        $vo = $this->executeConstruct();
        $this->assertInstanceOf('Resources\Domain\ValueObject\IntegerRange', $vo);
    }
    function test_construct_setMinValue() {
        $vo = $this->executeConstruct();
        $this->assertEquals($this->minValue, $vo->minValue);
    }
    function test_construct_nullMinValue_constructNormally() {
        $this->minValue = null;
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    function test_construct_setMaxValue() {
        $vo = $this->executeConstruct();
        $this->assertEquals($this->maxValue, $vo->maxValue);
    }
    function test_construct_nullMaxValue_constructNormally() {
        $this->maxValue = null;
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    function test_construct_minValueBiggerThanMaxValue_throwEx() {
        $this->minValue = $this->maxValue + 1;
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: max value must be bigger or equals  min value";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    function test_construct_emptyMaxValue_constructNormally() {
        $this->minValue = -99;
        $this->maxValue = 0;
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    function test_sameValueAs_compareToSameValueObject_returnTrue() {
        $this->assertTrue($this->vo->sameValueAs(new TestableIntegerRange($this->minValue, $this->maxValue)));
    }
    function test_sameValueAs_compareToDifferentValueObject_returnFalse() {
        $this->assertFalse($this->vo->sameValueAs(new TestableIntegerRange($this->minValue + 1, $this->maxValue + 1)));
    }
    function test_contain_containArgumentValue_returnTrue() {
        $this->assertTrue($this->vo->contain($this->minValue + 2));
    }
    function test_contain_argValueOutsideRange_returnFalse() {
        $this->assertFalse($this->vo->contain($this->minValue - 1));
    }
    function test_contain_inclusiveMinValue_returnTrue() {
        $this->assertTrue($this->vo->contain($this->minValue));
    }
    function test_contain_inclusiveMaxValue_returnTrue() {
        $this->assertTrue($this->vo->contain($this->maxValue));
    }
    function test_emptyMinValue_negativeArgValue_returnTrue() {
        $this->vo->minValue = 0;
        $this->assertTrue($this->vo->contain(23));
    }
    function test_emptyMaxValue_argBiggerThanMinValue_returnTrue() {
        $this->vo->maxValue = 0;
        $this->assertTrue($this->vo->contain($this->minValue + 12));
    }
}

class TestableIntegerRange extends IntegerRange{
    public $minValue, $maxValue;
}
