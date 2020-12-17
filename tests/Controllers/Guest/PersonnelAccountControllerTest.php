<?php

namespace Tests\Controllers\Guest;

use DateTimeImmutable;
use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\RecordOfFirm
};

class PersonnelAccountControllerTest extends ControllerTestCase
{
    protected $personnel;
    protected $personnel_withoutResetPasswordCode;
    protected $resetPasswordUri = "/api/guest/personnel-account/reset-password";
    protected $resetPasswordInput;
    protected $generateResetPasswordCodeUri = "/api/guest/personnel-account/generate-reset-password-code";
    protected $generateResetPasswordCodeInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Personnel")->truncate();
        
        $firm = new RecordOfFirm(0);
        $this->connection->table("Firm")->insert($firm->toArrayForDbEntry());
        
        $this->personnel = new RecordOfPersonnel($firm, 0);
        $this->connection->table("Personnel")->insert($this->personnel->toArrayForDbEntry());
        
        $this->resetPasswordInput = [
            "firmIdentifier" => $firm->identifier,
            "email" => $this->personnel->email,
            "resetPasswordCode" => $this->personnel->resetPasswordCode,
            "password" => "newPassword123",
        ];
        $this->generateResetPasswordCodeInput = [
            "firmIdentifier" => $firm->identifier,
            "email" => $this->personnel->email,
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Personnel")->truncate();
    }
    
    public function test_resetPassword_200()
    {
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(200);
        
        $loginUri = "/api/personnel-login";
        $loginInput = [
            "firmIdentifier" => $this->personnel->firm->identifier,
            "email" => $this->personnel->email,
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
        $this->connection->table("Personnel")->truncate();
        $this->personnel->resetPasswordCodeExpiredTime = (new DateTimeImmutable("-1 minutes"))->format("Y-m-d H:i:s");
        $this->connection->table("Personnel")->insert($this->personnel->toArrayForDbEntry());
        
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    public function test_resetPassword_emptyCode_403()
    {
        $this->connection->table("Personnel")->truncate();
        $this->personnel->resetPasswordCode = "";
        $this->connection->table("Personnel")->insert($this->personnel->toArrayForDbEntry());
        
        $this->resetPasswordInput["resetPasswordCode"] = "";
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    
    public function test_generateResetPasswordCode_200()
    {
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(200);
        
        $personnelEntry = [
            "id" => $this->personnel->id,
            "resetPasswordCodeExpiredTime" => (new \DateTimeImmutable("+24 hours"))->format("Y-m-d H:i:s")
        ];
        $this->seeInDatabase("Personnel", $personnelEntry);
    }
    public function test_generateResetPasswordCode_sendMail()
    {
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(200);
        
        $personnelMailEntry = [
            "Personnel_id" => $this->personnel->id,
        ];
        $this->seeInDatabase("PersonnelMail", $personnelMailEntry);
        
        $mailRecipientEntry = [
            "recipientMailAddress" => $this->personnel->email,
            "recipientName" => $this->personnel->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $mailRecipientEntry);
    }
}
