<?php

namespace User\Domain\Model;

use Resources\Domain\ValueObject\Password;
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
        $this->personnel->resetPasswordCodeExpiredTime = new \DateTimeImmutable("+1 hours");
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
        $this->personnel->resetPasswordCodeExpiredTime = (new \DateTimeImmutable("-1 minutes"));
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
    public function test_resetPasswordCode_unsetTokenAndExpiredTime()
    {
        $this->executeResetPassword();
        $this->assertNull($this->personnel->resetPasswordCode);
        $this->assertNull($this->personnel->resetPasswordCodeExpiredTime);
    }
}

class TestablePersonnel extends Personnel
{
    public $firm;
    public $id;
    public $name;
    public $email;
    public $password;
    public $phone;
    public $bio;
    public $resetPasswordCode;
    public $resetPasswordCodeExpiredTime;
    public $removed;
}
