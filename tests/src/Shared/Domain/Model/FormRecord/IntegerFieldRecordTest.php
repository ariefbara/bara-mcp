<?php

namespace Shared\Domain\Model\FormRecord;

use Shared\Domain\Model\ {
    Form\IntegerField,
    FormRecord
};
use Tests\TestBase;

class IntegerFieldRecordTest extends TestBase
{

    protected $formRecord;
    protected $integerField;
    protected $integerFieldRecord;
    protected $id = 'id', $value = 123;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->integerField = $this->buildMockOfClass(IntegerField::class);
        $this->integerFieldRecord = new TestableIntegerFieldRecord($this->formRecord, 'id', $this->integerField, 99);
    }

    function test_construct_setProperties()
    {
        $record = new TestableIntegerFieldRecord($this->formRecord, $this->id, $this->integerField, $this->value);
        $this->assertEquals($this->id, $record->id);
        $this->assertEquals($this->integerField, $record->integerField);
        $this->assertEquals($this->value, $record->value);
        $this->assertFalse($record->removed);
    }

    function test_update_changeValue()
    {
        $this->integerFieldRecord->update($value = 12312);
        $this->assertEquals($value, $this->integerFieldRecord->value);
    }

    function test_isReferToRemovedField_returnReferenceFieldRemovedStatus()
    {
        $this->integerField->expects($this->once())->method("isRemoved")->willReturn(true);
        $this->assertTrue($this->integerFieldRecord->isReferToRemovedField());
    }

    function test_remove_setRemovedTrue()
    {
        $this->integerFieldRecord->remove();
        $this->assertTrue($this->integerFieldRecord->removed);
    }

}

class TestableIntegerFieldRecord extends IntegerFieldRecord
{

    public $formRecord, $id, $integerField, $value, $removed;

}
