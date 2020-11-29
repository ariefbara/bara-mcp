<?php

namespace User\Domain\Model;

use Config\EventList;
use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\Event\CommonEvent,
    Domain\ValueObject\Password
};
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $personnel;
    protected $password;
    protected $resetPasswordCode = "resetPasswordCode", $newPassword = "newPwd123";

    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = new TestablePersonnel();
        
        $this->password = $this->buildMockOfClass(Password::class);
        $this->personnel->password = $this->password;
        
        $this->personnel->resetPasswordCode = $this->resetPasswordCode;
        $this->personnel->resetPasswordCodeExpiredTime = new DateTimeImmutable("+1 hours");
    }
    
    protected function executeResetPassword()
    {
        $this->personnel->resetPassword($this->resetPasswordCode, $this->newPassword);
    }
    public function test_resetPassword_changePassword()
    {
        $this->executeResetPassword();
        $this->assertTrue($this->personnel->password->match($this->newPassword));
    }
    public function test_resetPassword_invalidToken_forbidden()
    {
        $this->resetPasswordCode = "invalid token";
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbidden: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_resetPassword_expiredToken_forbidden()
    {
        $this->personnel->resetPasswordCodeExpiredTime = (new DateTimeImmutable("-1 minutes"));
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbidden: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_resetPassword_emptyToken_forbidden()
    {
        $this->personnel->resetPasswordCode = "";
        $this->resetPasswordCode = "";
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbidden: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_resetPassword_unsetTokenAndExpiredTime()
    {
        $this->executeResetPassword();
        $this->assertNull($this->personnel->resetPasswordCode);
        $this->assertNull($this->personnel->resetPasswordCodeExpiredTime);
    }
    public function test_resetPassword_failedAttempt_unsetTokenAndExpiredTime()
    {
        $this->personnel->resetPasswordCodeExpiredTime = (new DateTimeImmutable("-1 minutes"));
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbidden: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
        
        $this->assertNull($this->personnel->resetPasswordCode);
        $this->assertNull($this->personnel->resetPasswordCodeExpiredTime);
    }
    public function test_resetPassword_inactivePersonnel_forbidden()
    {
        $this->personnel->active = false;
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbiden: only active personnel can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeGenerateResetPasswordCode()
    {
        $this->personnel->generateResetPasswordCode();
    }
    public function test_generateResetPasswordCode_setResetPasswordCode()
    {
        $this->executeGenerateResetPasswordCode();
        $this->assertNotNull($this->personnel->resetPasswordCode);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy("+24 hours"), $this->personnel->resetPasswordCodeExpiredTime);
    }
    public function test_generateResetPasswordCode_recordEvent()
    {
        $this->executeGenerateResetPasswordCode();
        $event = new CommonEvent(EventList::PERSONNEL_RESET_PASSWORD_CODE_GENERATED, $this->personnel->id);
        $this->assertEquals($event, $this->personnel->recordedEvents[0]);
    }
    public function test_generateResetPasswordCode_inactivePersonnel_forbidden()
    {
        $this->personnel->active = false;
        $operation = function (){
            $this->executeGenerateResetPasswordCode();
        };
        $errorDetail = "forbiden: only active personnel can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestablePersonnel extends Personnel
{
    public $firm;
    public $id = "personnelId";
    public $name;
    public $email;
    public $password;
    public $phone;
    public $bio;
    public $resetPasswordCode;
    public $resetPasswordCodeExpiredTime;
    public $active = true;
    public $recordedEvents;
    
    function __construct()
    {
        parent::__construct();
    }
}
