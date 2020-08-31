<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Resources\Domain\ {
    Model\Mail\Recipient,
    ValueObject\PersonName
};
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $personnel;
    protected $firm;
    protected $id = 'personnel-input', $firstName = 'hadi', $lastName = 'pranoto', $email = 'newPersonnel@email.org', 
        $password = 'password123', $phone = '08231231231';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $personnelData = new PersonnelData('firstname', 'lastname', 'personnel@email.org', 'password123', '0812312312');
        $this->personnel = new TestablePersonnel($this->firm, 'id', $personnelData);
    }
    
    protected function executeConstruct()
    {
        return new TestablePersonnel($this->firm, $this->id, $this->getPersonnelData());
    }
    protected function getPersonnelData()
    {
        return new PersonnelData($this->firstName, $this->lastName, $this->email, $this->password, $this->phone);
    }
    
    public function test_construct_setProperties()
    {
        $personnel = $this->executeConstruct();
        $this->assertEquals($this->firm, $personnel->firm);
        $this->assertEquals($this->id, $personnel->id);
        
        $name = new PersonName($this->firstName, $this->lastName);
        $this->assertEquals($name, $personnel->name);
        
        $this->assertEquals($this->email, $personnel->email);
        $this->assertTrue($personnel->password->match($this->password));
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $personnel->joinTime->format('Y-m-d H:i:s'));
        $this->assertFalse($personnel->removed);
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
    
    public function test_getMailRecipient_returnRecipient()
    {
        $recipient = new Recipient($this->personnel->email, $this->personnel->name);
        $this->assertEquals($recipient, $this->personnel->getMailRecipient());
    }
    
    public function test_getName_returnFullName()
    {
        $this->assertEquals($this->personnel->name->getFullName(), $this->personnel->getName());
    }
}

class TestablePersonnel extends Personnel
{
    public $firm, $id, $name, $email, $password, $phone, $joinTime, $removed;
    public $assignedAdmin;
}
