<?php

namespace Firm\Domain\Model\Shared\Form;

use Firm\Domain\Model\Shared\Form;
use Resources\Domain\ValueObject\IntegerRange;
use Tests\TestBase;
use TypeError;

class IntegerFieldTest extends TestBase
{

    protected $integerField;
    protected $form;
    protected $id = 'new-id', $name = 'new name', $description = 'new description', $position = 'new position',
        $required = true, $defaultValue = 123, $minValue = 1, $maxValue = 99,
        $placeholder = 'new placeholder';
    
    protected $formRecord, $formRecordData, $value = 9;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->buildMockOfClass(Form::class);
        $fieldData = new FieldData('name', 'description', 'position', false);
        $integerFieldData = new IntegerFieldData($fieldData, null, null, '', 111);
        $this->integerField = new TestableIntegerField($this->form, 'id', $integerFieldData);
    }

    protected function getFieldData()
    {
        return new FieldData($this->name, $this->description, $this->position, $this->required);
    }

    protected function getIntegerFieldData()
    {
        return new IntegerFieldData($this->getFieldData(), $this->minValue, $this->maxValue, $this->placeholder,
            $this->defaultValue);
    }
    
    protected function executeConstruct()
    {
        return new TestableIntegerField($this->form, $this->id, $this->getIntegerFieldData());
    }

    public function test_construct_setProperties()
    {
        $integerField = $this->executeConstruct();
        $this->assertEquals($this->form, $integerField->form);
        $this->assertEquals($this->id, $integerField->id);
        $fieldVO = new FieldVO($this->getFieldData());
        $this->assertEquals($fieldVO, $integerField->fieldVO);
        $this->assertEquals($this->defaultValue, $integerField->defaultValue);
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $integerField->minMaxValue);
        $this->assertEquals($this->placeholder, $integerField->placeholder);
        $this->assertFalse($integerField->removed);
    }
    public function test_construct_nonIntegerDefaultValue_throwEx()
    {
        $this->defaultValue = 'non integer';
        $this->expectException(TypeError::class);
        $this->executeConstruct();
    }
    public function test_construct_nullDefaultValue_constructNormally()
    {
        $this->defaultValue = null;
        $this->executeConstruct();
        $this->markAsSuccess();
    }

    protected function executeUpdate()
    {
        $this->integerField->update($this->getIntegerFieldData());
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $fieldVO = new FieldVO($this->getFieldData());
        $this->assertEquals($fieldVO, $this->integerField->fieldVO);
        $this->assertEquals($this->defaultValue, $this->integerField->defaultValue);
        $minMaxValue = new IntegerRange($this->minValue, $this->maxValue);
        $this->assertEquals($minMaxValue, $this->integerField->minMaxValue);
        $this->assertEquals($this->placeholder, $this->integerField->placeholder);
    }
    public function test_update_nonIntegerDefaultValue_throwEx()
    {
        $this->defaultValue = 'non integer';
        $this->expectException(TypeError::class);
        $this->executeUpdate();
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->integerField->remove();
        $this->assertTrue($this->integerField->removed);
    }

}

class TestableIntegerField extends IntegerField
{

    public $form, $id, $fieldVO, $defaultValue, $minMaxValue, $placeholder, $removed;

}
