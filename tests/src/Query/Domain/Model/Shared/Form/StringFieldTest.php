<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class StringFieldTest extends TestBase
{
    protected $stringField;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->stringField = new TestableStringField();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_extractCorrespondingValueFromRecord_returnFormRecordsFileInfoListOfStringFieldRecordCorrespondWithStringField()
    {
        $this->formRecord->expects($this->once())
                ->method('getStringFieldRecordValueCorrespondWith')
                ->with($this->stringField);
        $this->stringField->extractCorrespondingValueFromRecord($this->formRecord);
    }
}

class TestableStringField extends StringField
{
    public function __construct()
    {
        
    }
}
