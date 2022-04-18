<?php

namespace Resources;

use Tests\TestBase;

class BaseEnumTest extends TestBase
{
    protected $enum;
    protected function setUp(): void
    {
        parent::setUp();
        $this->enum = new TestableBaseEnum(TestableBaseEnum::ONE);
    }
    
    public function test_construct_setProperties()
    {
        $enum = new TestableBaseEnum(TestableBaseEnum::ONE);
        $this->assertEquals(1, $enum->value);
    }
    public function test_construct_invalidValue_badRequest()
    {
        $operation = function (){
            new TestableBaseEnum(123);
        };
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', 'bad request: invalid enum argument');
    }
    
    protected function getValueName()
    {
        return $this->enum->getValueName();
    }
    public function test_getValueName_getConstantNameOfCorrespondingValue()
    {
        $this->assertEquals('ONE', $this->getValueName());
    }
}

class TestableBaseEnum extends BaseEnum
{
    public $value;
    
    const ONE = 1;
    const TWO = 'two';
}
