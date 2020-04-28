<?php

namespace Shared\Domain\Model\Form;

use Resources\Domain\ValueObject\IntegerRange;
use Shared\Domain\Model\ {
    Form\SelectField\Option,
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class MultiSelectFieldTest extends TestBase
{

    protected $multiSelectField;
    protected $selectField;
    protected $minMaxValue;
    
    protected $formRecord, $formRecordData;
    protected $option, $selectedOptionId = 'optionId';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->option = $this->buildMockOfClass(Option::class);
        
        $this->multiSelectField = new TestableMultiSelectField();
        $this->multiSelectField->id = 'id';
        $this->selectField = $this->buildMockOfClass(SelectField::class);
        $this->selectField->expects($this->any())
                ->method('getOptionOrDie')
                ->with($this->selectedOptionId)
                ->willReturn($this->option);
        $this->multiSelectField->selectField = $this->selectField;
        
        $this->minMaxValue = $this->buildMockOfClass(IntegerRange::class);
        $this->multiSelectField->minMaxValue = $this->minMaxValue;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->formRecordData->expects($this->any())
                ->method('getSelectedOptionIdListOf')
                ->with($this->multiSelectField->id)
                ->willReturn([$this->selectedOptionId]);
    }
    
    protected function executeSetMultiSelectFieldRecordOf()
    {
        $this->formRecordData->expects($this->any())
                ->method('getSelectedOptionIdListOf')
                ->with($this->multiSelectField->id)
                ->willReturn([$this->selectedOptionId]);
        $this->minMaxValue->expects($this->any())
                ->method('contain')
                ->willReturn(true);
        $this->multiSelectField->setMultiSelectFieldRecordOf($this->formRecord, $this->formRecordData);
    }
    public function test_setMultiSelectFieldRecordOf_executeFormRecordsSetMultiSelectFieldRecordMethod()
    {
        $this->formRecord->expects($this->once())
            ->method('setMultiSelectFieldRecord')
            ->with($this->multiSelectField, [$this->option]);
        $this->executeSetMultiSelectFieldRecordOf();
    }
    public function test_setMultiSelectFieldRecordOf_executeSelectFieldsAssertRequirementSatisfiedMethod()
    {
        $this->selectField->expects($this->once())
            ->method('assertMandatoryRequirementSatisfied')
            ->with([$this->selectedOptionId]);
        $this->executeSetMultiSelectFieldRecordOf();
    }
    public function test_setMultiSelectFieldRecordOf_selectedOptionCountExceedMaxValue_throwEx()
    {
        $this->minMaxValue->expects($this->once())
                ->method('contain')
                ->with(count([$this->option]))
                ->willReturn(false);
        $operation = function (){
            $this->executeSetMultiSelectFieldRecordOf();
        };
        $errorDetail = "bad request: selected option for {$this->multiSelectField->selectField->getName()} field is out of range";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_setMultiSelectFieldRecordOf_emptySelectedOption_processNormally()
    {
        $this->formRecordData->expects($this->any())
                ->method('getSelectedOptionIdListOf')
                ->with($this->multiSelectField->id)
                ->willReturn([]);
        $this->executeSetMultiSelectFieldRecordOf();
        $this->markAsSuccess();
    }

}

class TestableMultiSelectField extends MultiSelectField
{
    public $assignmentForm;
    public $id;
    public $selectField;
    public $minMaxValue;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
