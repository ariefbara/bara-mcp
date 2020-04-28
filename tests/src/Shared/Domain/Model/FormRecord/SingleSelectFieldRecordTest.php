<?php

namespace Shared\Domain\Model\FormRecord;

use Shared\Domain\Model\ {
    Form\SelectField\Option,
    Form\SingleSelectField,
    FormRecord
};
use Tests\TestBase;

class SingleSelectFieldRecordTest extends TestBase
{

    protected $formRecord, $singleSelectField;
    protected $singleSelectFieldRecord;
    protected $id = 'new-id', $option;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->singleSelectField = $this->buildMockOfClass(SingleSelectField::class);

        $option = $this->buildMockOfClass(Option::class);
        $this->singleSelectFieldRecord = new TestableSingleSelectFieldRecord($this->formRecord, 'id',
                $this->singleSelectField, $option);

        $this->option = $this->buildMockOfClass(Option::class);
    }

    protected function executeConstruct()
    {
        return new TestableSingleSelectFieldRecord($this->formRecord, $this->id, $this->singleSelectField,
                $this->option);
    }

    public function test_construct_setProperties()
    {
        $singleSelectFieldRecord = $this->executeConstruct();
        $this->assertEquals($this->formRecord, $singleSelectFieldRecord->formRecord);
        $this->assertEquals($this->id, $singleSelectFieldRecord->id);
        $this->assertEquals($this->singleSelectField, $singleSelectFieldRecord->singleSelectField);
        $this->assertEquals($this->option, $singleSelectFieldRecord->option);
        $this->assertFalse($singleSelectFieldRecord->removed);
    }

    public function test_construct_nullOption_processNormally()
    {
        $this->option = null;
        $singleSelectFieldRecord = $this->executeConstruct();
        $this->assertNull($singleSelectFieldRecord->option);
    }

    protected function executeUpdate()
    {
        $this->singleSelectFieldRecord->update($this->option);
    }

    public function test_update_changeOption()
    {
        $this->executeUpdate();
        $this->assertSame($this->option, $this->singleSelectFieldRecord->option);
    }

    public function test_update_nullOption_updateNormally()
    {
        $this->option = null;
        $this->executeUpdate();
        $this->assertNull($this->singleSelectFieldRecord->option);
    }

    public function test_isReferToRemovedField_returnSingleSelectFieldsIsRemovedMethod()
    {
        $this->singleSelectField->expects($this->once())
                ->method('isRemoved')
                ->willReturn(true);
        $this->assertTrue($this->singleSelectFieldRecord->isReferToRemovedField());
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->singleSelectFieldRecord->remove();
        $this->assertTrue($this->singleSelectFieldRecord->removed);
    }

}

class TestableSingleSelectFieldRecord extends SingleSelectFieldRecord
{

    public $formRecord, $id, $singleSelectField, $option, $removed;

}
