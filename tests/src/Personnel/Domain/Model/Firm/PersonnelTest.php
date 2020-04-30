<?php

namespace Personnel\Domain\Model\Firm;

use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $personnel;
    protected $name = 'new personnel name', $phone = '0821323123';
    protected $previousPassword = 'password123', $newPassword = 'newPwd123';


    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = new TestablePersonnel();
        $this->personnel->password = new Password($this->previousPassword);
    }
    
    protected function executeUpdate()
    {
        $this->personnel->updateProfile($this->name, $this->phone);
    }
    public function test_update_changeNameAndPhone()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->personnel->name);
        $this->assertEquals($this->phone, $this->personnel->phone);
    }
    public function test_update_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = 'bad request: personnel name is mandatory';
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_update_invalidPhoneFormat_throwEx()
    {
        $this->phone = 'invalidFormat';
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "bad request: personnel phone format is invalid";
        $this->assertRegularExceptionThrowed($operation, 'Bad Request', $errorDetail);
    }
    public function test_update_emptyPhone_updateNormally()
    {
        $this->phone = '';
        $this->executeUpdate();
        $this->markAsSuccess();
    }
    
    protected function executeChangePassword()
    {
        $this->personnel->changePassword($this->previousPassword, $this->newPassword);
    }
    public function test_changePassword_changePassword()
    {
        $this->executeChangePassword();
        $this->assertTrue($this->personnel->password->match($this->newPassword));
    }
    public function test_changePassword_previousPasswordNotMatch_throwEx()
    {
        $this->previousPassword = 'unmatchPassword';
        $operation = function (){
            $this->executeChangePassword();
        };
        $errorDetail = "forbidden: previous password not match";
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
}

class TestablePersonnel extends Personnel{
    public $incubator, $id, $name, $email, $password, $phone, $joinTime, $removed;
    public $programMentorships, $personnelFileInfos;
    
    public function __construct()
    {
        parent::__construct();
    }
}
