<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class TextAreaFieldTest extends TestBase
{
    protected $textAreaField;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->textAreaField = new TestableTextAreaField();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_extractCorrespondingValueFromRecord_returnFormRecordsFileInfoListOfTextAreaFieldRecordCorrespondWithTextAreaField()
    {
        $this->formRecord->expects($this->once())
                ->method('getTextAreaFieldRecordValueCorrespondWith')
                ->with($this->textAreaField);
        $this->textAreaField->extractCorrespondingValueFromRecord($this->formRecord);
    }
}

class TestableTextAreaField extends TextAreaField
{
    public function __construct()
    {
        
    }
}
