<?php

namespace Query\Domain\Model\Shared\FormRecord;

use Query\Domain\Model\Shared\Form\IntegerField;
use Tests\TestBase;

class IntegerFieldRecordTest extends TestBase
{
    protected $record;
    protected $field;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->record = new TestableIntegerFieldRecord();
        
        $this->field = $this->buildMockOfClass(IntegerField::class);
        $this->record->integerField = $this->field;
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
        $this->record->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->assertFalse($this->record->isActiveFieldRecordCorrespondWith($this->field));
    }
}

class TestableIntegerFieldRecord extends IntegerFieldRecord
{
    public $formRecord;
    public $id;
    public $integerField;
    public $value = null;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
