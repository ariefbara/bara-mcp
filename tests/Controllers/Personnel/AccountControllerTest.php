<?php

namespace Tests\Controllers\Personnel;

class AccountControllerTest extends PersonnelTestCase
{
    protected $updateProfileUri;
    protected $changePasswordUri;
    protected $updateProfileInput = [
        'firstName' => 'firstname',
        'lastName' => 'lastname',
        "phone" => '08123123123',
        "bio" => 'new personnel bio',
    ];
    protected $changePasswordInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->updateProfileUri = $this->personnelUri . "/update-profile";
        $this->changePasswordUri = $this->personnelUri . "/change-password";
        
        $this->changePasswordInput = [
            "previousPassword" => $this->personnel->rawPassword,
            "newPassword" => 'newPwd1234',
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
    }
    
    public function test_updateProfile()
    {
        $response = [
            "id" => $this->personnel->id,
            "name" => $this->updateProfileInput['firstName'] . " " . $this->updateProfileInput['lastName'],
            "phone" => $this->updateProfileInput['phone'],
            "bio" => $this->updateProfileInput['bio'],
        ];
        $this->patch($this->updateProfileUri, $this->updateProfileInput, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $personnelEntry = [
            "id" => $this->personnel->id,
            'firstName' => 'firstname',
            'lastName' => 'lastname',
            "phone" => $this->updateProfileInput['phone'],
            "bio" => $this->updateProfileInput['bio'],
        ];
        $this->seeInDatabase("Personnel", $personnelEntry);
    }
    public function test_updateProfile_emptyName_error400()
    {
        $this->updateProfileInput['firstName'] = '';
        $this->patch($this->updateProfileUri, $this->updateProfileInput, $this->personnel->token)
                ->seeStatusCode(400);
    }
    public function test_updateProfile_invalidPhoneFormat_error400()
    {
        $this->updateProfileInput['phone'] = 'invalid phone format';
        $this->patch($this->updateProfileUri, $this->updateProfileInput, $this->personnel->token)
                ->seeStatusCode(400);
    }
    public function test_updateProfile_emptyPhone_executeNormally()
    {
        $this->updateProfileInput['phone'] = null;
        $this->patch($this->updateProfileUri, $this->updateProfileInput, $this->personnel->token)
                ->seeStatusCode(200);
        $personnelEntry = [
            "id" => $this->personnel->id,
            "phone" => null,
        ];
        $this->seeInDatabase("Personnel", $personnelEntry);
    }
    
    public function test_changePassword()
    {
        $this->patch($this->changePasswordUri, $this->changePasswordInput, $this->personnel->token)
                ->seeStatusCode(200);
        
        $loginUri = "/api/personnel-login";
        $loginRequest = [
            "firmIdentifier" => $this->personnel->firm->identifier,
            "email" => $this->personnel->email,
            "password" => $this->changePasswordInput['newPassword'],
        ];
        $this->post($loginUri, $loginRequest)
                ->seeStatusCode(200);
    }
    public function test_changePassword_unmatchPreviousPassword_error403()
    {
        $this->changePasswordInput['previousPassword'] = 'unmatchedPassword';
        $this->patch($this->changePasswordUri, $this->changePasswordInput, $this->personnel->token)
                ->seeStatusCode(403);
    }
}
