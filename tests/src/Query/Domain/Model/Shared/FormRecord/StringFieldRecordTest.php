<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\Form\StringField;
use Tests\TestBase;

class StringFieldRecordTest extends TestBase
{
    protected $record;
    protected $field;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->record = new TestableStringFieldRecord();
        
        $this->field = $this->buildMockOfClass(StringField::class);
        $this->record->stringField = $this->field;
    }
    
    public function test_isActiveFieldRecordCorrespondWith_activeRecordCorrespondToSameField_returnTrue()
    {
        $this->assertTrue($this->record->isActiveFieldRecordCorrespondWith($this->field));
    }
    public function test_isActiveFieldRecordCorrespondWith_removedRecord_returnFalse()
    {
        $this->record->removed = true;
        $this->assertFalse($this->record->isActiveFieldRecordCorrespondWith($this->field));
    }
    public function test_isActiveFieldRecordCorrespondWith_differentField_returnFalse()
    {
        $this->record->stringField = $this->buildMockOfClass(StringField::class);
        $this->assertFalse($this->record->isActiveFieldRecordCorrespondWith($this->field));
    }
}

class TestableStringFieldRecord extends StringFieldRecord
{
    public $formRecord;
    public $id;
    public $stringField;
    public $value = null;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
