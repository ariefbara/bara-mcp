<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\Firm\RecordOfManager,
    RecordPreparation\RecordOfFirm
};

class ManagerAccountControllerTest extends ControllerTestCase
{
    protected $manager;
    protected $manager_withoutResetPasswordCode;
    protected $resetPasswordUri = "/api/guest/manager-account/reset-password";
    protected $resetPasswordInput;
    protected $generateResetPasswordCodeUri = "/api/guest/manager-account/generate-reset-password-code";
    protected $generateResetPasswordCodeInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Manager")->truncate();
        
        $firm = new RecordOfFirm(0);
        $this->connection->table("Firm")->insert($firm->toArrayForDbEntry());
        
        $this->manager = new RecordOfManager($firm, 0);
        $this->connection->table("Manager")->insert($this->manager->toArrayForDbEntry());
        
        $this->resetPasswordInput = [
            "firmIdentifier" => $firm->identifier,
            "email" => $this->manager->email,
            "resetPasswordCode" => $this->manager->resetPasswordCode,
            "password" => "newPassword123",
        ];
        $this->generateResetPasswordCodeInput = [
            "firmIdentifier" => $firm->identifier,
            "email" => $this->manager->email,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Manager")->truncate();
    }
    
    public function test_resetPassword_200()
    {
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(200);
        
        $loginUri = "/api/manager-login";
        $loginInput = [
            "firmIdentifier" => $this->manager->firm->identifier,
            "email" => $this->manager->email,
            "password" => $this->resetPasswordInput['password'],
        ];
        $this->post($loginUri, $loginInput)
            ->seeStatusCode(200);
    }
    public function test_resetPassword_invalidCode_403()
    {
        $this->resetPasswordInput["resetPasswordCode"] = "invalid Token";
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    public function test_resetPassword_expiredCode_403()
    {
        $this->connection->table("Manager")->truncate();
        $this->manager->resetPasswordCodeExpiredTime = (new \DateTimeImmutable("-1 minutes"))->format("Y-m-d H:i:s");
        $this->connection->table("Manager")->insert($this->manager->toArrayForDbEntry());
        
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    public function test_resetPassword_emptyCode_403()
    {
        $this->connection->table("Manager")->truncate();
        $this->manager->resetPasswordCode = "";
        $this->connection->table("Manager")->insert($this->manager->toArrayForDbEntry());
        
        $this->resetPasswordInput["resetPasswordCode"] = "";
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    
    public function test_generateResetPasswordCode_200()
    {
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(200);
        
        $managerEntry = [
            "id" => $this->manager->id,
            "resetPasswordCodeExpiredTime" => (new \DateTimeImmutable("+24 hours"))->format("Y-m-d H:i:s")
        ];
        $this->seeInDatabase("Manager", $managerEntry);
    }
    public function test_generateResetPasswordCode_sendMail()
    {
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(200);
        
        $managerMailEntry = [
            "Manager_id" => $this->manager->id,
        ];
        $this->seeInDatabase("ManagerMail", $managerMailEntry);
        
        $mailRecipientEntry = [
            "recipientMailAddress" => $this->manager->email,
            "recipientName" => $this->manager->name,
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $mailRecipientEntry);
    }
}
