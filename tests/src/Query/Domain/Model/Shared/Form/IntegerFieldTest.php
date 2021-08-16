<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class IntegerFieldTest extends TestBase
{
    protected $integerField;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->integerField = new TestableIntegerField();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_extractCorrespondingValueFromRecord_returnFormRecordsFileInfoListOfIntegerFieldRecordCorrespondWithIntegerField()
    {
        $this->formRecord->expects($this->once())
                ->method('getIntegerFieldRecordValueCorrespondWith')
                ->with($this->integerField);
        $this->integerField->extractCorrespondingValueFromRecord($this->formRecord);
    }
}

class TestableIntegerField extends IntegerField
{
    public function __construct()
    {
        
    }
}
