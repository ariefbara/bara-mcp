<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\Form\SelectField\Option;
use Query\Domain\Model\Shared\Form\SingleSelectField;
use Tests\TestBase;

class SingleSelectFieldRecordTest extends TestBase
{
    protected $singleSelectFieldRecord;
    protected $singleSelectField;
    protected $option;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->singleSelectFieldRecord = new TestableSingleSelectFieldRecord();
        
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->singleSelectFieldRecord->singleSelectField = $this->singleSelectField;
        
        $this->option = $this->buildMockOfClass(Option::class);
    }
    
    protected function isActiveFieldRecordCorrespondWith()
    {
        return $this->singleSelectFieldRecord->isActiveFieldRecordCorrespondWith($this->singleSelectField);
    }
    public function test_isActiveFieldRecordCorrespondWith_activeRecordCorrespondToSameField_returnTrue()
    {
        $this->assertTrue($this->isActiveFieldRecordCorrespondWith());
    }
    public function test_isActiveFieldRecordCorrespondWith_removedRecord_returnFalse()
    {
        $this->singleSelectFieldRecord->removed = true;
        $this->assertFalse($this->isActiveFieldRecordCorrespondWith());
    }
    public function test_isActiveFieldRecordCorrespondWith_differentField_returnFalse()
    {
        $this->singleSelectFieldRecord->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);
        $this->assertFalse($this->isActiveFieldRecordCorrespondWith());
    }
    
    public function test_getSelectedOptionName_returnOptionName()
    {
        $this->singleSelectFieldRecord->option = $this->option;
        $this->option->expects($this->once())
                ->method('getName')
                ->willReturn($optionName = 'option name');
        $this->assertEquals($optionName, $this->singleSelectFieldRecord->getSelectedOptionName());
    }
    public function test_getSelectedOptionName_noSelectedOption_returnNull()
    {
        $this->assertNull($this->singleSelectFieldRecord->getSelectedOptionName());
    }
}

class TestableSingleSelectFieldRecord extends SingleSelectFieldRecord
{
    public $formRecord;
    public $id;
    public $singleSelectField;
    public $option;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
