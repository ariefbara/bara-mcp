<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecord;

use SharedContext\Domain\Model\SharedEntity\ {
    Form\StringField,
    FormRecord
};
use Tests\TestBase;

class StringFieldRecordTest extends TestBase
{
    protected $formRecord;
    protected $stringField;
    protected $stringFieldRecord;
    
    protected $id = 'new id', $value = 'new string value';
    
    protected function setUp(): void {
        parent::setUp();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->stringField = $this->buildMockOfClass(StringField::class);
        
        $this->stringFieldRecord = new TestableStringFieldRecord($this->formRecord, 'id', $this->stringField, 'string value');
    }
    function test_construct_setProperties() {
        $stringFieldRecord = new TestableStringFieldRecord($this->formRecord, $this->id, $this->stringField, $this->value);
        $this->assertEquals($this->formRecord, $stringFieldRecord->formRecord);
        $this->assertEquals($this->id, $stringFieldRecord->id);
        $this->assertEquals($this->stringField, $stringFieldRecord->stringField);
        $this->assertEquals($this->value, $stringFieldRecord->value);
        $this->assertFalse($stringFieldRecord->removed);
    }
    
    function test_update_updateValue() {
        $this->stringFieldRecord->update($this->value);
        $this->assertEquals($this->value, $this->stringFieldRecord->value);
    }
    function test_isReferToRemovedField_returnFieldRemovedStatus() {
        $this->stringField->expects($this->once())
            ->method("isRemoved")
            ->willReturn(true);
        $this->assertTrue($this->stringFieldRecord->isReferToRemovedField());
    }
    function test_remove_setRemovedTrue() {
        $this->stringFieldRecord->remove();
        $this->assertTrue($this->stringFieldRecord->removed);
    }
    
}

class TestableStringFieldRecord extends StringFieldRecord{
    public $formRecord, $id, $stringField, $value, $removed;
}

