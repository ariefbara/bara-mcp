<?php

namespace SharedContext\Domain\Model\SharedEntity\Form;

use SharedContext\Domain\Model\SharedEntity\ {
    Form\SelectField\Option,
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class SingleSelectFieldTest extends TestBase
{

    protected $singleSelectField;
    protected $selectField;
    
    protected $formRecord, $formRecordData;
    protected $option, $selectedOptionid = 'optionId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->singleSelectField = new TestableSingleSelectField();
        $this->singleSelectField->id = 'id';
        
        $this->option = $this->buildMockOfClass(Option::class);
        $this->selectField = $this->buildMockOfClass(SelectField::class);
        $this->selectField->expects($this->any())
                ->method('getOptionOrDie')
                ->with($this->selectedOptionid)
                ->willReturn($this->option);
        $this->singleSelectField->selectField = $this->selectField;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeSetSingleSelectFieldRecordOf()
    {
        $this->formRecordData->expects($this->any())
                ->method('getSelectedOptionIdOf')
                ->with($this->singleSelectField->id)
                ->willReturn($this->selectedOptionid);
        $this->singleSelectField->setSingleSelectFieldRecordOf($this->formRecord, $this->formRecordData);
    }
    public function test_setSingleSelectFieldRecordOf_executeFormRecordsSetSingleSelectFieldRecordMethod()
    {
        $this->formRecord->expects($this->once())
            ->method('setSingleSelectFieldRecord')
            ->with($this->singleSelectField, $this->option);
        $this->executeSetSingleSelectFieldRecordOf();
    }
    public function test_setSingleSelectFieldRecordOf_executeSelectFieldAssertRequirementSatisfiedMethod()
    {
        $this->selectField->expects($this->once())
            ->method('assertMandatoryRequirementSatisfied')
            ->with($this->selectedOptionid);
        $this->executeSetSingleSelectFieldRecordOf();
    }
    public function test_setSingleSelectFieldRecordOf_emptyData_useNullAsSelectedOptionArgument()
    {
        $this->selectedOptionid = null;
        $this->formRecord->expects($this->once())
            ->method('setSingleSelectFieldRecord')
            ->with($this->singleSelectField, null);
        $this->executeSetSingleSelectFieldRecordOf();
    }

}

class TestableSingleSelectField extends SingleSelectField
{
    public $assignmentForm;
    public $id;
    public $selectField;
    public $defaultValue = null;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
