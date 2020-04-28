<?php

namespace Tests\Controllers;

use Tests\Controllers\RecordPreparation\RecordOfClient;

class NotLoggedClientAccountControllerTest extends ControllerTestCase
{

    protected $client;
    protected $activeClient;
    protected $clientAccountUri = "/api/client-account";

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();

        $this->client = new RecordOfClient(0, 'purnama.adi@gmail.com', 'password123');
        $this->activeClient = new RecordOfClient(1, 'adi@barapraja.com', 'password12345');
        $this->activeClient->activated = true;
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());
        $this->connection->table('Client')->insert($this->activeClient->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
    }
    public function test_activate()
    {
        $input = [
            'email' => $this->client->email,
            'activationCode' => $this->client->activationCode,
        ];
        $uri = "/api/client-activate";
        $this->patch($uri, $input)
            ->seeStatusCode(200);

        $clientRecord = [
            "id" => $this->client->id,
            "email" => $this->client->email,
            "activated" => true,
        ];
        $this->seeInDatabase("Client", $clientRecord);
    }

    public function test_activate_alreadyActivated_error403()
    {
        $input = [
            'email' => $this->activeClient->email,
            'activationCode' => $this->activeClient->activationCode,
        ];

        $uri = "/api/client-activate";
        $this->patch($uri, $input)
            ->seeStatusCode(403);
    }
    public function test_activate_invalidActivationCode_error400()
    {
        $input = [
            'email' => $this->client->email,
            'activationCode' => 'invalid activation code',
        ];
        $uri = "/api/client-activate";
        $this->patch($uri, $input)
            ->seeStatusCode(400);
    }
    
    public function test_resetPassword()
    {
        $input = [
            'email' => $this->activeClient->email,
            'resetPasswordCode' => $this->activeClient->resetPasswordCode,
            'password' => 'newPassword123',
        ];
        $uri = "/api/client-reset-password";
        $this->patch($uri, $input)
            ->seeStatusCode(200);
        
        $clientRecord = [
            "id" => $this->activeClient->id,
            "resetPasswordCode" => null,
            "resetPasswordCodeExpiredTime" => null,
        ];
        $this->seeInDatabase("Client", $clientRecord);
    }
    public function test_resetPassword_invalidCode_error400()
    {
        $input = [
            'email' => $this->activeClient->email,
            'resetPasswordCode' => 'invalid code',
            'password' => 'newPassword123',
        ];
        $uri = "/api/client-reset-password";
        $this->patch($uri, $input)
            ->seeStatusCode(400);
    }
    
    public function test_generateActivationCode()
    {
        
        $this->connection->table('Client')->truncate();
        $this->client->activationCode = null;
        $this->client->activationCodeExpiredTime = null;
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());
        
        $input = [
            'email' => $this->client->email,
        ];
        $uri = "/api/client-generate-activation-code";
        $this->patch($uri, $input)
            ->seeStatusCode(200);
//check record manually to assert operation
    }
    public function test_generateActivationCode_alreadyActive_error403()
    {
        $input = [
            'email' => $this->activeClient->email,
        ];
        $uri = "/api/client-generate-activation-code";
        $this->patch($uri, $input)
            ->seeStatusCode(403);
    }
    
    public function test_generateResetPasswordCode()
    {
        $this->connection->table('Client')->truncate();
        $this->client->resetPasswordCode = null;
        $this->client->resetPasswordCodeExpiredTime = null;
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());
        
        $input = [
            'email' => $this->client->email,
        ];
        $uri = "/api/client-generate-reset-password-code";
        $this->patch($uri, $input)
            ->seeStatusCode(200);
//check record manually to assert operation
    }

}
