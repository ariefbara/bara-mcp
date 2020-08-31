<?php

namespace SharedContext\Domain\Model\SharedEntity\FormRecord;

use SharedContext\Domain\Model\SharedEntity\ {
    Form\TextAreaField,
    FormRecord
};
use Tests\TestBase;

class TextAreaFieldRecordTest extends TestBase
{
    protected $formRecord;
    protected $textAreaField;
    protected $textAreaFieldRecord;
    protected $id = 'new-id', $value = 'new text area value';
    
    protected function setUp(): void {
        parent::setUp();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->textAreaField = $this->buildMockOfClass(TextAreaField::class);
        
        $this->textAreaFieldRecord = new TestableTextAreaFieldRecord($this->formRecord, 'id', $this->textAreaField, 'text area value');
    }
    function test_construct_setProperties() {
        $record = new TestableTextAreaFieldRecord($this->formRecord, $this->id, $this->textAreaField, $this->value);
        $this->assertEquals($this->formRecord, $record->formRecord);
        $this->assertEquals($this->textAreaField, $record->textAreaField);
        $this->assertEquals($this->id, $record->id);
        $this->assertEquals($this->value, $record->value);
        $this->assertFalse($record->removed);
    }
    
    function test_update_updateValue() {
        $this->textAreaFieldRecord->update($value = 'new value');
        $this->assertEquals($value, $this->textAreaFieldRecord->value);
    }
    function test_update_nullValue_executeNormally() {
        $this->textAreaFieldRecord->update(null);
        $this->markAsSuccess();
    }
    function test_isReferToRemovedField_returnFieldRemovedStatus() {
        $this->textAreaField->expects($this->once())->method('isRemoved')->willReturn(true);
        $this->assertTrue($this->textAreaFieldRecord->isReferToRemovedField());
    }
    function test_remove_setRemovedStatusTrue() {
        $this->textAreaFieldRecord->remove();
        $this->assertTrue($this->textAreaFieldRecord->removed);
    }
}

class TestableTextAreaFieldRecord extends TextAreaFieldRecord{
    public $formRecord, $id, $textAreaField, $value, $removed;
}

