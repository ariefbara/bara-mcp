<?php

namespace Tests\Controllers\Client;

class AccountControllerTest extends ClientTestCase
{
    protected $updateInput = [
        "firstName" => "hadi",
        "lastName" => "pranoto",
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
    
    protected function show()
    {
        $uri = $this->clientUri . '/profile';
        $this->get($uri, $this->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->client->id,
            'firstName' => $this->client->firstName,
            'lastName' => $this->client->lastName,
            'email' => $this->client->email,
        ];
        $this->seeJsonContains($response);
    }
    
    public function test_updateProfile()
    {
        $response = [
            "id" => $this->client->id,
            "firstName" => $this->updateInput['firstName'],
            "lastName" => $this->updateInput['lastName'],
            "email" => $this->client->email,
        ];
        $uri = $this->clientUri . "/update-profile";
        $this->patch($uri, $this->updateInput, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $clientRecord = [
            "id" => $this->client->id,
            "firstName" => $this->updateInput['firstName'],
            "lastName" => $this->updateInput['lastName'],
        ];
        $this->seeInDatabase('Client', $clientRecord);
    }
    public function test_updateProfile_emptyFirstName_400()
    {
        $this->updateInput['firstName'] = '';
        $uri = $this->clientUri . "/update-profile";
        $this->patch($uri, $this->updateInput, $this->client->token)
            ->seeStatusCode(400);
    }
    public function test_updateProfile_inactiveClient_403()
    {
        $uri = $this->clientUri . "/update-profile";
        $this->patch($uri, $this->updateInput, $this->inactiveClient->token)
            ->seeStatusCode(403);
    }
    
    public function test_changePassword()
    {
        $uri = $this->clientUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->client->token)
            ->seeStatusCode(200);
        
        $loginUri = "/api/client-login";
        $loginInput = [
            "firmIdentifier" => $this->client->firm->identifier,
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
    public function test_changePassword_inactiveClient_403()
    {
        $uri = $this->clientUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->inactiveClient->token)
            ->seeStatusCode(403);
    }
}
