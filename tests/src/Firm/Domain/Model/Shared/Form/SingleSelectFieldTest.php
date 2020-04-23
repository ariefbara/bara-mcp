<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Tests\TestBase;

class SingleSelectFieldTest extends TestBase
{
    protected $form;
    protected $selectField;
    protected $singleSelectField;
    protected $id = 'newId', $selectFieldData, $defaultValue = 'new default value';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->buildMockOfClass(Form::class);
        $selectFieldData = new SelectFieldData(new FieldData('name', 'description', 'position', false));
        $singleSelectFieldData = new SingleSelectFieldData($selectFieldData, 'default value');
        $this->singleSelectField = new TestableSingleSelectField($this->form, 'id', $singleSelectFieldData);
        
        $this->selectField = $this->buildMockOfClass(SelectField::class);
        $this->singleSelectField->selectField = $this->selectField;
        
        $this->selectFieldData = new SelectFieldData(new FieldData('new name', 'new description', 'new position', true));
    }
    
    protected function getSingleSelectFieldData()
    {
        return new SingleSelectFieldData($this->selectFieldData, $this->defaultValue);
    }
    public function test_construct_setProperties()
    {
        $singleSelectField = new TestableSingleSelectField(
                $this->form, $this->id, $this->getSingleSelectFieldData());
        
        $this->assertEquals($this->form, $singleSelectField->form);
        $this->assertEquals($this->id, $singleSelectField->id);
        $this->assertEquals($this->defaultValue, $singleSelectField->defaultValue);
        $this->assertFalse($singleSelectField->removed);
        
        $selectField = new SelectField($this->id, $this->selectFieldData);
        $this->assertEquals($selectField, $singleSelectField->selectField);
    }
    
    public function test_update_changeDefaultValue()
    {
        $this->singleSelectField->update($this->getSingleSelectFieldData());
        $this->assertEquals($this->defaultValue, $this->singleSelectField->defaultValue);
    }
    public function test_update_updateSelectField()
    {
        $this->selectField->expects($this->once())
                ->method('update')
                ->with($this->selectFieldData);
        $this->singleSelectField->update($this->getSingleSelectFieldData());
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->singleSelectField->remove();
        $this->assertTrue($this->singleSelectField->removed);
    }
}

class TestableSingleSelectField extends SingleSelectField{
    public $form, $id, $selectField, $defaultValue, $removed;
}
