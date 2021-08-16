<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\Form\TextAreaField;
use Tests\TestBase;

class TextAreaFieldRecordTest extends TestBase
{
    protected $record;
    protected $field;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->record = new TestableTextAreaFieldRecord();
        
        $this->field = $this->buildMockOfClass(TextAreaField::class);
        $this->record->textAreaField = $this->field;
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
        $this->record->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        $this->assertFalse($this->record->isActiveFieldRecordCorrespondWith($this->field));
    }
}

class TestableTextAreaFieldRecord extends TextAreaFieldRecord
{
    public $formRecord;
    public $id;
    public $textAreaField;
    public $value = null;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
