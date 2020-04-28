<?php

namespace Shared\Domain\Model\Form;

use Resources\Domain\ValueObject\IntegerRange;
use Shared\Domain\Model\ {
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class StringFieldTest extends TestBase
{
    
    protected $stringField;
    protected $field, $minMaxValue;
    
    protected $formRecord, $formRecordData, $value = 'string input';

    protected function setUp(): void
    {
        parent::setUp();
        $this->stringField = new TestableStringField();
        $this->stringField->id = 'id';
        
        $this->field = $this->buildMockOfClass(FieldVO::class);
        $this->stringField->field = $this->field;
        
        $this->minMaxValue = $this->buildMockOfClass(IntegerRange::class);
        $this->stringField->minMaxValue = $this->minMaxValue;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->formRecordData->expects($this->any())
                ->method('getStringFieldRecordDataOf')
                ->with($this->stringField->id)
                ->willReturn($this->value);
    }
    protected function executeSetStringFieldRecordOf()
    {
        $this->minMaxValue->expects($this->any())
                ->method('contain')
                ->willReturn(true);
        $this->stringField->setStringFieldRecordOf($this->formRecord, $this->formRecordData);
    }
    public function test_setStringFieldOf_executeFormRecordsSetStringFieldRecordMethod()
    {
        $this->formRecord->expects($this->once())
            ->method('setStringFieldRecord')
            ->with($this->stringField, $this->value);
        $this->executeSetStringFieldRecordOf();
    }
    public function test_setStringFieldOf_assertFieldRequirementSatisfied()
    {
        $this->field->expects($this->once())
                ->method('assertMandatoryRequirementSatisfied')
                ->with($this->value);
        $this->executeSetStringFieldRecordOf();
    }
    public function test_setStringFieldOf_stringDataLenghtOutsideMinMaxValue_throwEx()
    {
        $this->minMaxValue->expects($this->once())
                ->method('contain')
                ->with(strlen($this->value))
                ->willReturn(false);
        $operation = function (){
            $this->executeSetStringFieldRecordOf();
        };
        $errorDetail = "bad request: invalid input length for {$this->stringField->field->getName()} field";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }

}

class TestableStringField extends StringField
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
