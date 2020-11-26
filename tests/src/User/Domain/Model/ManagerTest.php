<?php

namespace User\Domain\Model;

use Config\EventList;
use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\Event\CommonEvent,
    Domain\ValueObject\Password
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Tests\TestBase;
use User\Domain\Model\Manager\ManagerFileInfo;

class ManagerTest extends TestBase
{
    protected $manager;
    protected $password;
    protected $manager_withResetPasswordCode;
    protected $resetPasswordCode = "resetPasswordCode";
    protected $managerFileInfoId = "managerFileInfoId", $fileInfoData;
    protected $oldPassword = "oldPwd123", $newPassword = "newPwd123";

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TestableManager();
        $this->password = $this->buildMockOfClass(Password::class);
        $this->manager->password = $this->password;
        
        $this->manager_withResetPasswordCode = new TestableManager();
        $this->manager_withResetPasswordCode->resetPasswordCode = $this->resetPasswordCode;
        $this->manager_withResetPasswordCode->resetPasswordCodeExpiredTime = new DateTimeImmutable("+12 hours");
        
        $this->fileInfoData = $this->buildMockOfClass(FileInfoData::class);
        $this->fileInfoData->expects($this->any())->method("getName")->willReturn("filename.txt");
    }
    
    protected function executeSaveFileInfo()
    {
        return $this->manager->saveFileInfo($this->managerFileInfoId, $this->fileInfoData);
    }
    public function test_saveFileInfo_returnManagerFileInfo()
    {
        $managerFileInfo = new ManagerFileInfo($this->manager, $this->managerFileInfoId, $this->fileInfoData);
        $this->assertEquals($managerFileInfo, $this->executeSaveFileInfo());
    }
    public function test_saveFileInfo_removedManager_forbidden()
    {
        $this->manager->removed = true;
        $operation = function (){
            $this->executeSaveFileInfo();
        };
        $errorDetail = "forbidden: only active manage can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeChangePassword()
    {
        $this->password->expects($this->any())
                ->method("match")
                ->willReturn(true);
        $this->manager->changePassword($this->oldPassword, $this->newPassword);
    }
    
    public function test_changePassword_changeManagerPassword()
    {
        $password = new Password($this->newPassword);
        $this->executeChangePassword();
        $this->assertTrue($this->manager->password->match($this->newPassword));
    }
    public function test_changePassword_oldPasswordDoesntMatchCurrentPassword_forbidden()
    {
        $this->password->expects($this->once())
                ->method("match")
                ->with($this->oldPassword)
                ->willReturn(false);
        $operation = function (){
            $this->executeChangePassword();
        };
        $errorDetail = "forbidden: provided password doesn't match current password";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeGenerateResetPasswordCode()
    {
        $this->manager->generateResetPasswordCode();
    }
    public function test_generateResetPasswordCode_setResetPasswordCode()
    {
        $this->executeGenerateResetPasswordCode();
        $this->assertNotNull($this->manager->resetPasswordCode);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy("+24 hours"), $this->manager->resetPasswordCodeExpiredTime);
    }
    public function test_generateResetPasswordCode_recordEvent()
    {
        $this->executeGenerateResetPasswordCode();
        $event = new CommonEvent(EventList::MANAGER_RESET_PASSWORD_CODE_GENERATED, $this->manager->id);
        $this->assertEquals($event, $this->manager->recordedEvents[0]);
    }
    
    protected function executeResetPassword()
    {
        $this->manager_withResetPasswordCode->resetPassword($this->resetPasswordCode, $this->newPassword);
    }
    public function test_resetPassword_changePassword()
    {
        $this->executeResetPassword();
        $this->assertTrue($this->manager_withResetPasswordCode->password->match($this->newPassword));
    }
    public function test_resetPassword_emptyResetPasswordCode_forbidden()
    {
        $this->manager_withResetPasswordCode->resetPasswordCode = "";
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbidden: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_resetPassword_unmatchedToken_forbidden()
    {
        $this->manager_withResetPasswordCode->resetPasswordCode = "unmatchToken";
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbidden: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_resetPassword_expiredToken_forbidden()
    {
        $this->manager_withResetPasswordCode->resetPasswordCodeExpiredTime = new \DateTimeImmutable("-1 hours");
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbidden: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_resetPasswordCode_clearResetPasswordCode()
    {
        $this->executeResetPassword();
        $this->assertNull($this->manager_withResetPasswordCode->resetPasswordCode);
        $this->assertNull($this->manager_withResetPasswordCode->resetPasswordCodeExpiredTime);
    }
    public function test_resetPasswordCode_failedAttend_clearResetPasswordCoreParameters()
    {
        $this->manager_withResetPasswordCode->resetPasswordCode = "invalid token";
        $operation = function (){
            $this->executeResetPassword();
        };
        $errorDetail = "forbidden: invalid or expired token";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
        
        $this->assertNull($this->manager_withResetPasswordCode->resetPasswordCode);
        $this->assertNull($this->manager_withResetPasswordCode->resetPasswordCodeExpiredTime);
    }
}

class TestableManager extends Manager
{
    public $firmId;
    public $id = "managerId";
    public $name;
    public $email;
    public $password;
    public $phone;
    public $removed = false;
    public $resetPasswordCode;
    public $resetPasswordCodeExpiredTime;
    public $recordedEvents;
    
    function __construct()
    {
        parent::__construct();
    }
}
