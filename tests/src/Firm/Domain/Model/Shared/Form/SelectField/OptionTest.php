<?php

namespace Firm\Domain\Model\Shared\Form\SelectField;

use Firm\Domain\Model\Shared\Form\SelectField;
use Tests\TestBase;

class OptionTest extends TestBase
{
    protected $selectField;
    protected $option;
    
    protected $id = 'newId', $name = 'new name', $description = 'new description', $position = 'new position';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->selectField = $this->buildMockOfClass(SelectField::class);
        $optionData = new OptionData('name', 'description', 'position');
        $this->option = new TestableOption($this->selectField, 'id', $optionData);
    }
    protected function getOptionData()
    {
        return new OptionData($this->name, $this->description, $this->position);
    }
    protected function executeConstruct()
    {
        return new TestableOption($this->selectField, $this->id, $this->getOptionData());
    }
    public function test_construct_scenario_expectedResult()
    {
        $option = $this->executeConstruct();
        $this->assertEquals($this->selectField, $option->selectField);
        $this->assertEquals($this->id, $option->id);
        $this->assertEquals($this->name, $option->name);
        $this->assertEquals($this->description, $option->description);
        $this->assertEquals($this->position, $option->position);
        $this->assertFalse($option->removed);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: option name is mandatory';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    
    protected function executeUpdate()
    {
        $this->option->update($this->getOptionData());
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->option->name);
        $this->assertEquals($this->description, $this->option->description);
        $this->assertEquals($this->position, $this->option->position);
    }
    public function test_update_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = 'bad request: option name is mandatory';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->option->remove();
        $this->assertTrue($this->option->removed);
    }
}

class TestableOption extends Option
{

    public $selectField, $id, $name, $description, $position, $removed;

}
