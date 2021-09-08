<?php

namespace Query\Domain\Model\Shared\Form;

use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class IntegerFieldTest extends TestBase
{
    protected $integerField;
    protected $containFieldRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->integerField = new TestableIntegerField();
        $this->containFieldRecord = $this->buildMockOfInterface(IContainFieldRecord::class);
    }
    
    public function test_extractCorrespondingValueFromRecord_returnFormRecordsFileInfoListOfIntegerFieldRecordCorrespondWithIntegerField()
    {
        $this->containFieldRecord->expects($this->once())
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
