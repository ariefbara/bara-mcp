<?php

namespace SharedContext\Domain\Model\SharedEntity\Form;

use Resources\Domain\ValueObject\IntegerRange;
use SharedContext\Domain\Model\SharedEntity\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class TextAreaFieldTest extends TestBase
{
    
    protected $textAreaField;
    protected $field, $minMaxValue;
    
    protected $formRecord, $formRecordData, $value = 'text area input';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->textAreaField = new TestableTextAreaField(); 
        $this->textAreaField->id = 'id';
        
        $this->field = $this->buildMockOfClass(FieldVO::class);
        $this->textAreaField->field = $this->field;
        
        $this->minMaxValue = $this->buildMockOfClass(IntegerRange::class);
        $this->textAreaField->minMaxValue = $this->minMaxValue;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->formRecordData->expects($this->any())
                ->method('getTextAreaFieldRecordDataOf')
                ->with($this->textAreaField->id)
                ->willReturn($this->value);
    }
    protected function executeSetTextAreaFieldRecordOf()
    {
        $this->minMaxValue->expects($this->any())
                ->method('contain')
                ->willReturn(true);
        $this->textAreaField->setTextAreaFieldRecordOf($this->formRecord, $this->formRecordData);
    }
    public function test_setTextAreaFieldRecord_executeFormRecordsSetTextAreaFieldRecordMethod()
    {
        $this->formRecord->expects($this->once())
            ->method('setTextAreaFieldRecord')
            ->with($this->textAreaField, $this->value);
        $this->executeSetTextAreaFieldRecordOf();
    }
    public function test_setTextAreaFieldRecord_executeFieldsAssertRequirementSatisfiedMethod()
    {
        $this->field->expects($this->once())
            ->method('assertMandatoryRequirementSatisfied')
            ->with($this->value);
        $this->executeSetTextAreaFieldRecordOf();
    }
    public function test_setTextAreaFieldRecord_inputLengthOutsideMinMaxValue_throwEx()
    {
        $this->minMaxValue->expects($this->once())
                ->method('contain')
                ->with(strlen($this->value))
                ->willReturn(false);
        $operation = function (){
            $this->executeSetTextAreaFieldRecordOf();
        };
        $errorDetail = "bad request: input value for {$this->textAreaField->field->getName()} field is out of range";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

}

class TestableTextAreaField extends TextAreaField
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
