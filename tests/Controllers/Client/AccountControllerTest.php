<?php

namespace Tests\Controllers\Client;

class AccountControllerTest extends ClientTestCase
{
    protected $updateInput = [
        "name" => "new client name",
    ];
    protected $changePasswordInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->changePasswordInput = [
            "previousPassword" => $this->client->rawPassword,
            "newPassword" => 'newPassword12345',
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_update()
    {
        $response = [
            "id" => $this->client->id,
            "name" => $this->updateInput['name'],
            "email" => $this->client->email,
        ];
        $uri = $this->clientUri . "/update-profile";
        $this->patch($uri, $this->updateInput, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $clientRecord = [
            "id" => $this->client->id,
            "name" => $this->updateInput['name'],
        ];
        $this->seeInDatabase('Client', $clientRecord);
    }
    
    public function test_changePassword()
    {
        $uri = $this->clientUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->client->token)
            ->seeStatusCode(200);
        
        $loginUri = "/api/client-login";
        $loginInput = [
            "email" => $this->client->email,
            "password" => $this->changePasswordInput['newPassword'],
        ];
        $this->post($loginUri, $loginInput)
            ->seeStatusCode(200);
        
    }
    public function test_changePassword_unmatchedPreviousPassword_error403()
    {
        $this->changePasswordInput['previousPassword'] = 'unmatched-password';
        $uri = $this->clientUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->client->token)
            ->seeStatusCode(403);
    }
}
