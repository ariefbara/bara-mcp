<?php

namespace Firm\Domain\Model\Shared\Form;

use Tests\TestBase;

class FieldVOTest extends TestBase
{
    protected $name = 'new name', $description = 'new description', $position = 'new position', $mandatory = true;
    
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    protected function getFieldData()
    {
        return new FieldData($this->name, $this->description, $this->position, $this->mandatory);
    }
    protected function executeConstruct()
    {
        return new TestableFieldVO($this->getFieldData());
    }
    public function test_construct_setProperties()
    {
        $fieldVO = $this->executeConstruct();
        $this->assertEquals($this->name, $fieldVO->name);
        $this->assertEquals($this->description, $fieldVO->description);
        $this->assertEquals($this->position, $fieldVO->position);
        $this->assertEquals($this->mandatory, $fieldVO->mandatory);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: field name is mandatory';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_construct_nonBooleanMandatory_castAsBoolVal()
    {
        $this->mandatory = 2313123;
        $fieldVO = $this->executeConstruct();
        $this->assertTrue($fieldVO->mandatory);
    }
}

class TestableFieldVO extends FieldVO
{

    public $name, $description, $position, $mandatory;

}
