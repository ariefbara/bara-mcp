<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class SingleSelectFieldTest extends TestBase
{
    protected $singleSelectField;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->singleSelectField = new TestableSingleSelectField();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_extractCorrespondingValueFromRecord_returnFormRecordsFileInfoListOfSingleSelectFieldRecordCorrespondWithSingleSelectField()
    {
        $this->formRecord->expects($this->once())
                ->method('getSingleSelectFieldRecordSelectedOptionNameCorrespondWith')
                ->with($this->singleSelectField);
        $this->singleSelectField->extractCorrespondingValueFromRecord($this->formRecord);
    }
}

class TestableSingleSelectField extends SingleSelectField
{
    public function __construct()
    {
        
    }
}
