<?php

namespace Tests\Controllers\Admin;

use Tests\Controllers\RecordPreparation\RecordOfAdmin;

class ProfileControllerTest extends AdminTestCase
{
    protected $otherAdmin;
    protected $updateRequest = [
        "name" => 'new admin name',
        "email" => 'new_address@email.org',
    ];
    protected $changePasswordRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->otherAdmin = new RecordOfAdmin('other', 'other_admin_address@email.org', 'password123');
        $this->connection->table('Admin')->insert($this->otherAdmin->toArrayForDbEntry());
        
        $this->changePasswordRequest = [
            "previousPassword" => $this->admin->rawPassword,
            "newPassword" => 'newPwd123',
        ];
    }
    
    public function test_updateProfile()
    {
        $response = [
            "id" => $this->admin->id,
            "name" => $this->updateRequest['name'],
            "email" => $this->updateRequest['email'],
        ];
        
        $uri = $this->adminUri . "/update-profile";
        $this->patch($uri, $this->updateRequest, $this->admin->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $adminRecord = [
            "id" => $this->admin->id,
            "name" => $this->updateRequest['name'],
            "email" => $this->updateRequest['email'],
        ];
        $this->seeInDatabase('Admin', $adminRecord);
    }
    public function test_updateProflie_emailAlreadyRegistered_error409()
    {
        $this->updateRequest['email'] = $this->otherAdmin->email;
        $uri = $this->adminUri . "/update-profile";
        $this->patch($uri, $this->updateRequest, $this->admin->token)
            ->seeStatusCode(409);
    }
    public function test_updateProfile_unchangedEmail_processNormally()
    {
        $this->updateRequest['email'] = $this->admin->email;
        $uri = $this->adminUri . "/update-profile";
        $this->patch($uri, $this->updateRequest, $this->admin->token)
            ->seeStatusCode(200);
    }
    
    public function test_changePassword()
    {
        $uri = $this->adminUri . "/change-password";
        $this->patch($uri, $this->changePasswordRequest, $this->admin->token)
            ->seeStatusCode(200);
        
        $loginUri = "/api/admin-login";
        $request = [
            "email" => $this->admin->email,
            "password" => $this->changePasswordRequest['newPassword'],
        ];
        $this->post($loginUri, $request)
            ->seeStatusCode(200);
    }
    public function test_changePassword_unmatchedPreviousPassword_error403()
    {
        $this->changePasswordRequest['previousPassword'] = 'unmatched_password';
        $uri = $this->adminUri . "/change-password";
        $this->patch($uri, $this->changePasswordRequest, $this->admin->token)
            ->seeStatusCode(403);
    }
}
