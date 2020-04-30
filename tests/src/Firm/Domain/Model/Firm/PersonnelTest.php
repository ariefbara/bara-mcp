<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $personnel;
    protected $firm;
    protected $id = 'personnel-input', $name = 'new personnel name', $email = 'newPersonnel@email.org', 
        $password = 'password123', $phone = '08231231231';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $personnelData = new PersonnelData('name', 'personnel@email.org', 'password123', '0812312312');
        $this->personnel = new TestablePersonnel($this->firm, 'id', $personnelData);
    }
    
    protected function executeConstruct()
    {
        return new TestablePersonnel($this->firm, $this->id, $this->getPersonnelData());
    }
    protected function getPersonnelData()
    {
        return new PersonnelData($this->name, $this->email, $this->password, $this->phone);
    }
    
    public function test_construct_setProperties()
    {
        $personnel = $this->executeConstruct();
        $this->assertEquals($this->firm, $personnel->firm);
        $this->assertEquals($this->id, $personnel->id);
        $this->assertEquals($this->name, $personnel->name);
        $this->assertEquals($this->email, $personnel->email);
        $this->assertTrue($personnel->password->match($this->password));
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $personnel->joinTime->format('Y-m-d H:i:s'));
        $this->assertFalse($personnel->removed);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: personnel name is required";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_invalidEmail_throwEx()
    {
        $this->email = 'invalid format';
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: personnel email is required in valid format";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    
    public function test_constructi_invalidPhoneFormat_throwEx()
    {
        $this->phone = "invalid format";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: personnel phone format is invalid";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_emptyPhone_void()
    {
        $this->phone = null;
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    
    public function test_passwordMatch_returnResultOfPasswordsMatchOperation()
    {
        $password = $this->buildMockOfClass(Password::class);
        $this->personnel->password = $password;
        
        $password->expects($this->once())
                ->method('match')
                ->with($password = 'password12312')
                ->willReturn(true);
        $this->assertTrue($this->personnel->passwordMatches($password));
    }
}

class TestablePersonnel extends Personnel
{
    public $firm, $id, $name, $email, $password, $phone, $joinTime, $removed;
    public $assignedAdmin;
}
