<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;
use Tests\TestBase;

class MultiSelectFieldTest extends TestBase
{
    protected $form;
    protected $selectField;
    protected $multiSelectField;
    
    protected $id = 'newId', $selectFieldData, $minValue = 16, $maxValue = 256;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->buildMockOfClass(Form::class);
        $selectFieldData = new SelectFieldData(new FieldData('name', 'description', 'position', false));
        $multiSelectFieldData = new MultiSelectFieldData($selectFieldData, 0, 0);
        $this->multiSelectField = new TestableMultiSelectField($this->form, 'id', $multiSelectFieldData);
        
        $this->selectField = $this->buildMockOfClass(SelectField::class);
        $this->multiSelectField->selectField = $this->selectField;
        
        $this->selectFieldData = new SelectFieldData(new FieldData('new name', 'new description', 'new position', true));
    }
    protected function getMultiSelectFieldData()
    {
        return new MultiSelectFieldData($this->selectFieldData, $this->minValue, $this->maxValue);
    }
    public function test_construct_setProperties()
    {
        $multiSelectField = new TestableMultiSelectField(
                $this->form, $this->id, $this->getMultiSelectFieldData());
        
        $this->assertEquals($this->form, $multiSelectField->form);
        $this->assertEquals($this->id, $multiSelectField->id);
        $this->assertFalse($multiSelectField->removed);
        
        $selectField = new SelectField($this->id, $this->selectFieldData);
        $this->assertEquals($selectField, $multiSelectField->selectField);
        
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $multiSelectField->minMaxValue);
    }
    
    public function test_update_updateMinMaxValue()
    {
        $this->multiSelectField->update($this->getMultiSelectFieldData());
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $this->multiSelectField->minMaxValue);
    }
    public function test_update_updateSelectField()
    {
        $this->selectField->expects($this->once())
                ->method('update')
                ->with($this->selectFieldData);
        $this->multiSelectField->update($this->getMultiSelectFieldData());
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->multiSelectField->remove();
        $this->assertTrue($this->multiSelectField->removed);
    }
}

class TestableMultiSelectField extends MultiSelectField
{

    public $form, $id, $selectField, $minMaxValue, $removed;

}
