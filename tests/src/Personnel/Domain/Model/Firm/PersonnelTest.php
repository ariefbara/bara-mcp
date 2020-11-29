<?php

namespace Personnel\Domain\Model\Firm;

use Resources\Domain\ValueObject\ {
    Password,
    PersonName
};
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $personnel;
    protected $firstName = 'firstname', $lastName = 'lastname', $phone = '0821323123';
    protected $bio = "new bio";
    protected $previousPassword = 'password123', $newPassword = 'newPwd123';


    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = new TestablePersonnel();
        $this->personnel->password = new Password($this->previousPassword);
    }
    
    protected function executeUpdate()
    {
        $data = new PersonnelProfileData($this->firstName, $this->lastName, $this->phone, $this->bio);
        $this->personnel->updateProfile($data);
    }
    public function test_update_changeNamePhoneAndBio()
    {
        $this->executeUpdate();
        $name = new PersonName($this->firstName, $this->lastName);
        $this->assertEquals($name, $this->personnel->name);
        $this->assertEquals($this->phone, $this->personnel->phone);
        $this->assertEquals($this->bio, $this->personnel->bio);
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
    public function test_update_inactivePersonnel_forbidden()
    {
        $this->personnel->active = false;
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "forbidden: only active personnel can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
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
    public function test_changePassword_inactivePersonnel_forbidden()
    {
        $this->personnel->active = false;
        $operation = function (){
            $this->executeChangePassword();
        };
        $errorDetail = "forbidden: only active personnel can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestablePersonnel extends Personnel{
    public $incubator, $id, $name, $email, $password, $phone, $joinTime, $active = true;
    public $bio;
    public $programMentorships, $personnelFileInfos;
    
    public function __construct()
    {
        parent::__construct();
    }
}
