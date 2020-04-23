<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;
use Tests\TestBase;

class StringFieldTest extends TestBase
{
    protected $stringField;
    protected $form;
    protected $id = 'newId', $fieldData, $minValue = 16, $maxValue = 128, $defaultValue = 'new default value', 
            $placeholder = 'new placeholder'; 
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->buildMockOfClass(Form::class);
        $fieldData = new FieldData('name', 'description', 'position', false);
        $stringFieldData = new StringFieldData($fieldData, null, null, '', '');
        $this->stringField = new TestableStringField($this->form, 'id', $stringFieldData);
        
        $this->fieldData = new FieldData('new name', 'new description', 'new position', true);
    }
    protected function getStringFieldData()
    {
        return new StringFieldData(
                $this->fieldData, $this->minValue, $this->maxValue, $this->placeholder, $this->defaultValue);
    }
    public function test_construct_setProperties()
    {
        $stringField = new TestableStringField($this->form, $this->id, $this->getStringFieldData());
        $this->assertEquals($this->form, $stringField->form);
        $this->assertEquals($this->id, $stringField->id);
        $this->assertEquals($this->defaultValue, $stringField->defaultValue);
        $this->assertEquals($this->placeholder, $stringField->placeholder);
        $this->assertFalse($stringField->removed);
        
        $fieldVO = new FieldVO($this->fieldData);
        $this->assertEquals($fieldVO, $stringField->fieldVO);
        
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $stringField->minMaxValue);
    }
    
    public function test_update_changeProperties()
    {
        $this->stringField->update($this->getStringFieldData());
        $this->assertEquals($this->defaultValue, $this->stringField->defaultValue);
        $this->assertEquals($this->placeholder, $this->stringField->placeholder);
        
        $fieldVO = new FieldVO($this->fieldData);
        $this->assertEquals($fieldVO, $this->stringField->fieldVO);
        
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $this->stringField->minMaxValue);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->stringField->remove();
        $this->assertTrue($this->stringField->removed);
    }
}

class TestableStringField extends StringField
{

    public $form, $id, $fieldVO, $minMaxValue, $placeholder, $defaultValue, $removed = false;

}
