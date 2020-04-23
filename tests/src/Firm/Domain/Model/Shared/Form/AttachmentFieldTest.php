<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;
use Tests\TestBase;

class AttachmentFieldTest extends TestBase
{
    protected $form;
    protected $attachmentField;
    protected $id = 'newId', $fieldData, $minValue = 1, $maxValue = 2;


    protected function setUp(): void
    {
        parent::setUp();
        $fieldData = new FieldData('name', 'description', 'position', false);
        $this->form = $this->buildMockOfClass(Form::class);
        $attachmentFieldData = new AttachmentFieldData($fieldData, null, null);
        $this->attachmentField = new TestableAttachmentField($this->form, 'id', $attachmentFieldData);
        
        $this->fieldData = new FieldData('new name', 'new description', 'new position', true);
    }
    protected function getAttachmentFieldData()
    {
        return new AttachmentFieldData($this->fieldData, $this->minValue, $this->maxValue);
    }
    public function test_construct_setProperties()
    {
        $attachmentField = new TestableAttachmentField($this->form, $this->id, $this->getAttachmentFieldData());
        $this->assertEquals($this->form, $attachmentField->form);
        $this->assertEquals($this->id, $attachmentField->id);
        $fieldVO = new FieldVO($this->fieldData);
        $this->assertEquals($fieldVO, $attachmentField->fieldVO);
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $attachmentField->minMaxValue);
        $this->assertFalse($attachmentField->removed);
    }
    
    public function test_update_changeProperties_expectedResult()
    {
        $this->attachmentField->update($this->getAttachmentFieldData());
        $fieldVO = new FieldVO($this->fieldData);
        $this->assertEquals($fieldVO, $this->attachmentField->fieldVO);
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $this->attachmentField->minMaxValue);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->attachmentField->remove();
        $this->assertTrue($this->attachmentField->removed);
    }
}

class TestableAttachmentField extends AttachmentField{
    public $form, $id, $fieldVO, $minMaxValue, $removed;
}
