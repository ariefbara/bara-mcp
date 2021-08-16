<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class MultiSelectFieldTest extends TestBase
{
    protected $multiSelectField;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->multiSelectField = new TestableMultiSelectField();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_extractCorrespondingValueFromRecord_returnFormRecordsFileInfoListOfMultiSelectFieldRecordCorrespondWithMultiSelectField()
    {
        $this->formRecord->expects($this->once())
                ->method('getListOfMultiSelectFieldRecordSelectedOptionNameCorrespondWith')
                ->with($this->multiSelectField);
        $this->multiSelectField->extractCorrespondingValueFromRecord($this->formRecord);
    }
}

class TestableMultiSelectField extends MultiSelectField
{
    public $form;
    public $id;
    public $selectField;
    public $minMaxValue;
    public $removed = false;
    
    public function __construct()
    {
        
    }
}
