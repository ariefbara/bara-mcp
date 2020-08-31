<?php

namespace Resources\Domain\ValueObject;

use Tests\TestBase;

class PersonNameTest extends TestBase
{
    protected $personName;
    protected $firstName = 'first';
    protected $lastName = 'last';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personName = new TestablePersonName('hadi', 'pranoto');
    }
    
    protected function executeConstruct()
    {
        return new TestablePersonName($this->firstName, $this->lastName);
    }
    
    public function test_construct_setProperties()
    {
        $personName = $this->executeConstruct();
        $this->assertEquals($this->firstName, $personName->firstName);
        $this->assertEquals($this->lastName, $personName->lastName);
    }
    public function test_construct_emptyFirstName_badRequest()
    {
        $this->firstName = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: first name is mandatory';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_getFullName_returnFullName()
    {
        $fullName = $this->personName->firstName . " " . $this->personName->lastName;
        $this->assertEquals($fullName, $this->personName->getFullName());
    }
    public function test_getFullName_emptyLastName_returnFirstNameOnly()
    {
        $this->personName->lastName = '';
        $this->assertEquals($this->personName->firstName, $this->personName->getFullName());
    }
}

class TestablePersonName extends PersonName
{
    public $firstName;
    public $lastName;
}
