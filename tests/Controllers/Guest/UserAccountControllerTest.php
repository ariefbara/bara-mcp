<?php

namespace Tests\Controllers\Guest;

use Tests\Controllers\{
    ControllerTestCase,
    RecordPreparation\RecordOfUser
};

class UserAccountControllerTest extends ControllerTestCase
{

    protected $inactiveUser;
    protected $activeUser;
    protected $activateUri = "/api/guest/user-account/activate";
    protected $activateInput;
    protected $resetPasswordUri = "/api/guest/user-account/reset-password";
    protected $resetPasswordInput;
    protected $generateActivationCodeUri = "/api/guest/user-account/generate-activation-code";
    protected $generateActivationCodeInput;
    protected $generateResetPasswordCodeUri = "/api/guest/user-account/generate-reset-password-code";
    protected $generateResetPasswordCodeInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('User')->truncate();

        $this->activeUser = new RecordOfUser(0);
        $this->activeUser->email = 'purnama.adi@gmail.com';
        $this->inactiveUser = new RecordOfUser(1);
        $this->inactiveUser->email = 'adi@barapraja.com';
        $this->inactiveUser->activated = false;
        $this->connection->table('User')->insert($this->activeUser->toArrayForDbEntry());
        $this->connection->table('User')->insert($this->inactiveUser->toArrayForDbEntry());

        $this->activateInput = [
            'email' => $this->inactiveUser->email,
            'activationCode' => $this->inactiveUser->activationCode,
        ];
        $this->generateActivationCodeInput = [
            'email' => $this->inactiveUser->email,
        ];
        $this->resetPasswordInput = [
            'email' => $this->activeUser->email,
            'resetPasswordCode' => $this->activeUser->resetPasswordCode,
            'password' => 'newPwd123',
        ];
        $this->generateResetPasswordCodeInput = [
            'email' => $this->activeUser->email,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('User')->truncate();
    }

    public function test_activate()
    {
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(200);

        $userRecord = [
            'id' => $this->inactiveUser->id,
            'activated' => true,
            'activationCode' => null,
            'activationCodeExpiredTime' => null,
        ];
        $this->seeInDatabase('User', $userRecord);
    }

    public function test_activate_invalidCode_403()
    {
        $this->activateInput['activationCode'] = 'invalid';
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(403);
    }

    public function test_activate_expiredCode_403()
    {
        $this->inactiveUser->activationCodeExpiredTime = (new \DateTimeImmutable('-1 second'))->format('Y-m-d H:i:s');
        $this->connection->table('User')->truncate();
        $this->connection->table('User')->insert($this->inactiveUser->toArrayForDbEntry());

        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(403);
    }

    public function test_activate_noExistingToken_403()
    {
        $this->inactiveUser->activationCode = "";
        $this->inactiveUser->activationCodeExpiredTime = null;
        $this->connection->table('User')->truncate();
        $this->connection->table('User')->insert($this->inactiveUser->toArrayForDbEntry());

        $this->activateInput['activationCode'] = $this->inactiveUser->activationCode;
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(403);
    }

    public function test_activate_alreadyActiveUser_403()
    {
        $this->activateInput['email'] = $this->activeUser->email;
        $this->activateInput['activationCode'] = $this->activeUser->activationCode;
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(403);
    }

    public function test_resetPassword()
    {
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(200);

        $userRecord = [
            'id' => $this->activeUser->id,
            'resetPasswordCode' => null,
            'resetPasswordCodeExpiredTime' => null,
        ];
        $this->seeInDatabase('User', $userRecord);

        $userLoginUri = '/api/user-login';
        $userLoginInput = [
            'email' => $this->activeUser->email,
            'password' => $this->resetPasswordInput['password'],
        ];
        $this->post($userLoginUri, $userLoginInput)
                ->seeStatusCode(200);
    }

    public function test_resetPassword_noExistingToken_403()
    {
        $this->activeUser->resetPasswordCode = "";
        $this->activeUser->resetPasswordCodeExpiredTime = null;
        $this->connection->table('User')->truncate();
        $this->connection->table('User')->insert($this->activeUser->toArrayForDbEntry());
        
        $this->resetPasswordInput['resetPasswordCode'] = $this->activeUser->resetPasswordCode;
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }

    public function test_resetPassword_invalidToken_403()
    {
        $this->resetPasswordInput['resetPasswordCode'] = 'invalid';
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }

    public function test_resetPassword_expiredToken_403()
    {
        $this->activeUser->resetPasswordCodeExpiredTime = (new \DateTimeImmutable('-1 second'));
        $this->connection->table('User')->truncate();
        $this->connection->table('User')->insert($this->activeUser->toArrayForDbEntry());
        
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }

    public function test_resetPassword_inactiveAccount_403()
    {
        $resetPasswordInput = [
            'email' => $this->inactiveUser->email,
            'resetPasswordCode' => $this->inactiveUser->resetPasswordCode,
            'password' => 'newPwd123',
        ];
        $this->patch($this->resetPasswordUri, $resetPasswordInput)
                ->seeStatusCode(403);
    }
    
    public function test_generateActivationCode()
    {
$this->disableExceptionHandling();
        $this->inactiveUser->activationCode = null;
        $this->inactiveUser->activationCodeExpiredTime = null;
        $this->connection->table('User')->truncate();
        $this->connection->table('User')->insert($this->inactiveUser->toArrayForDbEntry());
        
        $this->patch($this->generateActivationCodeUri, $this->generateActivationCodeInput)
                ->seeStatusCode(200);
        
        $userRecord = [
            'id' => $this->inactiveUser->id,
            'activationCodeExpiredTime' => (new \DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s'),
        ];
        $this->seeInDatabase('User', $userRecord);
//check email to see mail notification
    }
    public function test_generateActivationCode_scenario_expectedResult()
    {
        $generateActivationCodeInput = [
            'email' => $this->activeUser->email,
        ];
        $this->patch($this->generateActivationCodeUri, $generateActivationCodeInput)
                ->seeStatusCode(403);
    }
    public function test_generateActivationCode_sendMailNotificationToClient()
    {
        $this->patch($this->generateActivationCodeUri, $this->generateActivationCodeInput)
                ->seeStatusCode(200);
        $mailEntry = [
            "subject" => "Aktivasi Akun",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $userMailEntry = [
            "User_id" => $this->inactiveUser->id,
        ];
        $this->seeInDatabase("UserMail", $userMailEntry);
        
        $mailRecipientEntry = [
            "recipientMailAddress" => $this->inactiveUser->email,
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $mailRecipientEntry);
    }
    
    public function test_generateResetPasswordCode()
    {
        $this->activeUser->resetPasswordCode = null;
        $this->activeUser->resetPasswordCodeExpiredTime = null;
        $this->connection->table('User')->truncate();
        $this->connection->table('User')->insert($this->activeUser->toArrayForDbEntry());
        
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(200);
        
        $userRecord = [
            'id' => $this->activeUser->id,
            'resetPasswordCodeExpiredTime' => (new \DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s'),
        ];
        $this->seeInDatabase('User', $userRecord);
//check email to see mail notification
    }
    public function test_generateResetPasswordCode_inactiveUser_403()
    {
        $generateResetPasswordCodeInput = [
            'email' => $this->inactiveUser->email,
        ];
        $this->patch($this->generateResetPasswordCodeUri, $generateResetPasswordCodeInput)
                ->seeStatusCode(403);
    }
    public function test_generateResetPasswordCode_sendMailNotificationToUser()
    {
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(200);
        $mailEntry = [
            "subject" => "Konsulta: Reset Password",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $userMailEntry = [
            "User_id" => $this->activeUser->id,
        ];
        $this->seeInDatabase("UserMail", $userMailEntry);
        
        $mailRecipientEntry = [
            "recipientMailAddress" => $this->activeUser->email,
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $mailRecipientEntry);
    }


}
