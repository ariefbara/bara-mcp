<?php

namespace Tests\Controllers\User;

class AccountControllerTest extends UserTestCase
{
    protected $accountControllerUri;
    protected $changeProfileInput = [
        'firstName' => 'hadi',
        'lastName' => 'pranoto',
    ];
    protected $changePasswordInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->changePasswordInput = [
            'previousPassword' => $this->user->rawPassword,
            'newPassword' => 'newPassword123',
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_changeProfile()
    {
        $response = [
            'id' => $this->user->id,
            'firstName' => $this->changeProfileInput['firstName'],
            'lastName' => $this->changeProfileInput['lastName'],
            'email' => $this->user->email,
        ];
        
        $uri = $this->userUri . "/change-profile";
        $this->patch($uri, $this->changeProfileInput, $this->user->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $userRecord = [
            'id' => $this->user->id,
            'firstName' => $this->changeProfileInput['firstName'],
            'lastName' => $this->changeProfileInput['lastName'],
        ];
        $this->seeInDatabase('User', $userRecord);
    }
    public function test_changProfile_emptyFirstName_400()
    {
        $this->changeProfileInput['firstName'] = '';
        
        $uri = $this->userUri . "/change-profile";
        $this->patch($uri, $this->changeProfileInput, $this->user->token)
                ->seeStatusCode(400);
    }
    public function test_changeProfile_inactiveAccount_403()
    {
        $uri = $this->userUri . "/change-profile";
        $this->patch($uri, $this->changeProfileInput, $this->inactiveUser->token)
                ->seeStatusCode(403);
    }
    
    public function test_changePassword()
    {
        $uri = $this->userUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->user->token)
                ->seeStatusCode(200);
        
        $userLoginUri = '/api/user-login';
        $userLoginInput = [
            'email' => $this->user->email,
            'password' => $this->changePasswordInput['newPassword'],
        ];
        $this->post($userLoginUri, $userLoginInput)
                ->seeStatusCode(200);
    }
    public function test_changePassword_unmatchPreviousPassword_403()
    {
        $this->changePasswordInput['previousPassword'] = 'unmatch';
        $uri = $this->userUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->user->token)
                ->seeStatusCode(403);
    }
    public function test_changePassword_inactiveUser_403()
    {
        $uri = $this->userUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->inactiveUser->token)
                ->seeStatusCode(403);
    }
}
