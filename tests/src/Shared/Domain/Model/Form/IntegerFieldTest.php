<?php

namespace Shared\Domain\Model\Form;

use Resources\Domain\ValueObject\IntegerRange;
use Shared\Domain\Model\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class IntegerFieldTest extends TestBase
{

    protected $integerField, $id = 'id', $field, $minMaxValue;
    protected $formRecord, $formRecordData;
    protected $value = 9;

    protected function setUp(): void
    {
        parent::setUp();
        $this->integerField = new TestableIntegerField();
        $this->integerField->id = $this->id;
        $this->field = $this->buildMockOfClass(FieldVO::class);
        $this->integerField->field = $this->field;
        $this->minMaxValue = $this->buildMockOfClass(IntegerRange::class);
        $this->integerField->minMaxValue = $this->minMaxValue;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeSetIntegerFieldRecordOf()
    {
        $this->formRecordData->expects($this->once())
                ->method('getIntegerFieldRecordDataOf')
                ->with($this->id)
                ->willReturn($this->value);
        $this->minMaxValue->expects($this->any())
                ->method('contain')
                ->willReturn(true);
        $this->integerField->setIntegerFieldRecordOf($this->formRecord, $this->formRecordData);
    }
    public function test_setIntegerFieldRecordOf_setIntegerFieldRecordOfAssignmentFormRecord()
    {
        $this->formRecord->expects($this->once())
            ->method('setIntegerFieldRecord')
            ->with($this->integerField, $this->value);
        $this->executeSetIntegerFieldRecordOf();
    }
    public function test_setIntegerFieldRecordOf_executeFieldsAssertRequirementSatisfied()
    {
        $this->field->expects($this->once())
            ->method('assertMandatoryRequirementSatisfied')
            ->with($this->value);
        $this->executeSetIntegerFieldRecordOf();
    }
    public function test_setIntegerFieldRecordOf_inputOutsideMinMaxValue_throwEx()
    {
        $this->minMaxValue->expects($this->once())
                ->method('contain')
                ->with($this->value)
                ->willReturn(false);
        $operation = function (){
            $this->executeSetIntegerFieldRecordOf();
        };
        $errorDetail = "bad request: input value of {$this->integerField->field->getName()} field is out of range";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_setIntegerFieldRecordOf_nonIntegerValue_throwEx()
    {
        $this->value = 'non integer';
        $operation = function (){
            $this->executeSetIntegerFieldRecordOf();
        };
        $errorDetail = "bad request: input value of {$this->integerField->field->getName()} field is out of range";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
/*
    public function test_setIntegerFieldRecordOf_nullValue_processNormally()
    {
        $this->value = null;
        $this->executeSetIntegerFieldRecordOf();
        $this->markAsSuccess();
    }
    
    public function test_setIntegerFieldRecordOf_nullMaxValueWithDataValueBiggerThanMinValue_processNormally()
    {
        $this->integerField->minMaxValue = new IntegerRange(10, 0);
        $this->value = 20;
        $this->executeSetIntegerFieldRecordOf();
        $this->markAsSuccess();
    }
 * 
 */

}

class TestableIntegerField extends IntegerField
{
    public $assignmentForm;
    public $id;
    public $field;
    public $defaultValue = null;
    public $minMaxValue;
    public $placeholder = null;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
