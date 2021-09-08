<?php

namespace Tests\Controllers\Guest;

use DateTimeImmutable;
use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\RecordOfFirm
};

class ClientAccountControllerTest extends ControllerTestCase
{

    protected $firm;
    protected $inactiveClient;
    protected $activeClient;
    
    protected $activateUri = "/api/guest/client-account/activate";
    protected $activateInput;
    protected $resetPasswordUri = "/api/guest/client-account/reset-password";
    protected $resetPasswordInput;
    protected $generateActivationCodeUri = "/api/guest/client-account/generate-activation-code";
    protected $generateActivationCodeInput;
    protected $generateResetPasswordCodeUri = "/api/guest/client-account/generate-reset-password-code";
    protected $generateResetPasswordCodeInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientMail')->truncate();
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();

        $this->firm = new RecordOfFirm(0, 'firm-0-identifier');
        $this->connection->table('Firm')->insert($this->firm->toArrayForDbEntry());

        $this->inactiveClient = new RecordOfClient($this->firm, 0);
        $this->inactiveClient->activated = false;
        $this->activeClient = new RecordOfClient($this->firm, 1);
        $this->connection->table('Client')->insert($this->inactiveClient->toArrayForDbEntry());
        $this->connection->table('Client')->insert($this->activeClient->toArrayForDbEntry());
        
        $this->activateInput = [
            'firmIdentifier' => $this->firm->identifier,
            'email' => $this->inactiveClient->email,
            'activationCode' => $this->inactiveClient->activationCode,
        ];
        $this->resetPasswordInput = [
            'firmIdentifier' => $this->firm->identifier,
            'email' => $this->activeClient->email,
            'resetPasswordCode' => $this->activeClient->resetPasswordCode,
            'password' => 'newPwd123',
        ];
        $this->generateActivationCodeInput = [
            'firmIdentifier' => $this->firm->identifier,
            'email' => $this->inactiveClient->email,
        ];
        $this->generateResetPasswordCodeInput = [
            'firmIdentifier' => $this->firm->identifier,
            'email' => $this->activeClient->email,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientMail')->truncate();
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
    }
    
    public function test_activate()
    {
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(200);
        
        $clientRecord = [
            'id' => $this->inactiveClient->id,
            'activated' => true,
            'activationCode' => null,
            'activationCodeExpiredTime' => null,
        ];
        $this->seeInDatabase('Client', $clientRecord);
    }
    public function test_activate_accountAlreadyActivated_403()
    {
        $this->activateInput['email'] = $this->activeClient->email;
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(403);
    }
    public function test_activate_unmatchActivationCode_403()
    {
        $this->activateInput['activationCode'] = 'unmatch-code';
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(403);
    }
    public function test_activate_expiredActvationCode_403()
    {
        $this->connection->table('Client')->truncate();
        $this->inactiveClient->activationCodeExpiredTime = (new DateTimeImmutable('-1 second'))->format('Y-m-d H:i:s');
        $this->connection->table('Client')->insert($this->inactiveClient->toArrayForDbEntry());
        
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(403);
    }
    
    public function test_activate_emptyActivationCode_403()
    {
        $this->connection->table('Client')->truncate();
        $this->inactiveClient->activationCode = "";
        $this->connection->table('Client')->insert($this->inactiveClient->toArrayForDbEntry());
        
        $this->activateInput['activationCode'] = $this->inactiveClient->activationCode;
        $this->patch($this->activateUri, $this->activateInput)
                ->seeStatusCode(403);
    }
    
    public function test_resetPassword()
    {
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(200);
        
        $clientRecord = [
            'id' => $this->activeClient->id,
            'resetPasswordCode' => null,
            'resetPasswordCodeExpiredTime' => null,
        ];
        $this->seeInDatabase('Client', $clientRecord);
        
        $clientLoginUri = '/api/client-login';
        $clientLoginInput = [
            'firmIdentifier' => $this->activeClient->firm->identifier,
            'email' => $this->activeClient->email,
            'password' => $this->resetPasswordInput['password'],
        ];
        $this->post($clientLoginUri, $clientLoginInput)
                ->seeStatusCode(200);
    }
    public function test_resetPassword_unmatchedCode_403()
    {
        $this->resetPasswordInput['resetPasswordCode'] = 'unmatched';
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    public function test_resetPassword_emptyResetCode_403()
    {
        $this->activeClient->resetPasswordCode = "";
        $this->connection->table('Client')->truncate();
        $this->connection->table('Client')->insert($this->activeClient->toArrayForDbEntry());
        
        $this->resetPasswordInput['resetPasswordCode'] = $this->activeClient->resetPasswordCode;
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    public function test_resetPassword_expiredResetPasswordCode_403()
    {
        $this->activeClient->resetPasswordCodeExpiredTime = (new DateTimeImmutable('-1 second'))->format('Y-m-d H:i:s');
        $this->connection->table('Client')->truncate();
        $this->connection->table('Client')->insert($this->activeClient->toArrayForDbEntry());
        
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    public function test_resetPassword_inactiveAccount_403()
    {
        $this->resetPasswordInput['email'] = $this->inactiveClient->email;
        $this->resetPasswordInput['resetPasswordCode'] = $this->inactiveClient->resetPasswordCode;
        
        $this->patch($this->resetPasswordUri, $this->resetPasswordInput)
                ->seeStatusCode(403);
    }
    
    public function test_generateActivationCode()
    {
        $this->inactiveClient->activationCode = null;
        $this->inactiveClient->activationCodeExpiredTime = null;
        $this->connection->table('Client')->truncate();
        $this->connection->table('Client')->insert($this->inactiveClient->toArrayForDbEntry());
        
$expiredTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        
        $this->patch($this->generateActivationCodeUri, $this->generateActivationCodeInput)
                ->seeStatusCode(200);
        
        $clientRecord = [
            'id' => $this->inactiveClient->id,
            'activationCodeExpiredTime' => $expiredTime,
        ];
        $this->seeInDatabase('Client', $clientRecord);
    }
    public function test_generateActivationCode_activeAccount_403()
    {
        $this->generateActivationCodeInput['email'] = $this->activeClient->email;
        $this->patch($this->generateActivationCodeUri, $this->generateActivationCodeInput)
                ->seeStatusCode(403);
    }
    public function test_generateActivationCode_sendMailNotificationToClient()
    {
        $this->patch($this->generateActivationCodeUri, $this->generateActivationCodeInput)
                ->seeStatusCode(200);
        $mailEntry = [
            "subject" => "Activate Account",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $clientMailEntry = [
            "Client_id" => $this->inactiveClient->id,
        ];
        $this->seeInDatabase("ClientMail", $clientMailEntry);
        
        $mailRecipientEntry = [
            "recipientMailAddress" => $this->inactiveClient->email,
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $mailRecipientEntry);
    }
    
    public function test_generateResetPasswordCode()
    {
        $this->activeClient->resetPasswordCode = null;
        $this->activeClient->resetPasswordCodeExpiredTime = null;
        $this->connection->table('Client')->truncate();
        $this->connection->table('Client')->insert($this->activeClient->toArrayForDbEntry());
        
        $expiredTime = (new \DateTime('+24 hours'))->format('Y-m-d H:i:s');
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(200);
        
        $clientRecord = [
            'id' => $this->activeClient->id,
            'resetPasswordCodeExpiredTime' => $expiredTime,
        ];
        $this->seeInDatabase('Client', $clientRecord);
//check mail to verify notification send
    }
    public function test_generateResetPasswordCode_inactiveClient_403()
    {
        $this->generateResetPasswordCodeInput['email'] = $this->inactiveClient->email;
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(403);
    }
    public function test_generateResetPasswordCode_sendMailNotificationToClient()
    {
        $this->patch($this->generateResetPasswordCodeUri, $this->generateResetPasswordCodeInput)
                ->seeStatusCode(200);
        $mailEntry = [
            "subject" => "Your Account Recovery Is Ready",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $clientMailEntry = [
            "Client_id" => $this->activeClient->id,
        ];
        $this->seeInDatabase("ClientMail", $clientMailEntry);
        
        $mailRecipientEntry = [
            "recipientMailAddress" => $this->activeClient->email,
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $mailRecipientEntry);
    }

}
