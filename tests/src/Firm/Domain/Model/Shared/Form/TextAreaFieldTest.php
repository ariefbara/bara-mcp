<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;
use Tests\TestBase;

class TextAreaFieldTest extends TestBase
{
    protected $textAreaField;
    protected $form;
    protected $id = 'newId', $fieldData, $minValue = 16, $maxValue = 128, $defaultValue = 'new default value', 
            $placeholder = 'new placeholder';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->buildMockOfClass(Form::class);
        $fieldData = new FieldData('name', 'description', 'position', false);
        $textAreaFieldData = new TextAreaFieldData($fieldData, null, null, '', '');
        $this->textAreaField = new TestableTextAreaField($this->form, 'id', $textAreaFieldData);
        
        $this->fieldData = new FieldData('new name', 'new description', 'new position', true);
    }
    
    protected function getTextAreaFieldData()
    {
        return new TextAreaFieldData(
                $this->fieldData, $this->minValue, $this->maxValue, $this->placeholder, $this->defaultValue);
    }
    
    public function test_construct_setProperties()
    {
        $textAreaField = new TestableTextAreaField($this->form, $this->id, $this->getTextAreaFieldData());
        $this->assertEquals($this->form, $textAreaField->form);
        $this->assertEquals($this->id, $textAreaField->id);
        $this->assertEquals($this->defaultValue, $textAreaField->defaultValue);
        $this->assertEquals($this->placeholder, $textAreaField->placeholder);
        $this->assertFalse($textAreaField->removed);
        
        $fieldVO = new FieldVO($this->fieldData);
        $this->assertEquals($fieldVO, $textAreaField->fieldVO);
        
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $textAreaField->minMaxValue);
    }
    
    public function test_update_changeProperties()
    {
        $this->textAreaField->update($this->getTextAreaFieldData());
        $this->assertEquals($this->defaultValue, $this->textAreaField->defaultValue);
        $this->assertEquals($this->placeholder, $this->textAreaField->placeholder);
        
        $fieldVO = new FieldVO($this->fieldData);
        $this->assertEquals($fieldVO, $this->textAreaField->fieldVO);
        
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $this->textAreaField->minMaxValue);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->textAreaField->remove();
        $this->assertTrue($this->textAreaField->removed);
    }
}

class TestableTextAreaField extends TextAreaField{
    public $form, $id, $fieldVO, $minMaxValue, $placeholder, $defaultValue, $removed;
}
