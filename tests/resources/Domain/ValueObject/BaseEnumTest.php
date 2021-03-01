<?php

namespace Resources;

use Tests\TestBase;

class BaseEnumTest extends TestBase
{
    protected function setUp(): void
    {
        parent::setUp();
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
}

class TestableBaseEnum extends BaseEnum
{
    public $value;
    
    const ONE = 1;
    const TWO = 'two';
}
