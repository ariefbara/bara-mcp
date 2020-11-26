<?php

namespace Tests\Controllers\Manager;

class AccountControllerTest extends ManagerTestCase
{
    protected $accountUri;
    protected $changePasswordInput;


    protected function setUp(): void
    {
        parent::setUp();
        $this->accountUri = $this->managerUri . "/account";
        $this->changePasswordInput = [
            "oldPassword" => $this->manager->rawPassword,
            "newPassword" => "newPassword123",
        ];
    }
    
    public function test_changePassword_200()
    {
        $uri = $this->accountUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->manager->token)
                ->seeStatusCode(200);
        
        $loginUri = "/api/manager-login";
        $loginInput = [
            "firmIdentifier" => $this->manager->firm->identifier,
            "email" => $this->manager->email,
            "password" => $this->changePasswordInput['newPassword'],
        ];
        $this->post($loginUri, $loginInput)
            ->seeStatusCode(200);
    }
    public function test_changePassword_oldPasswordNotMatch_403()
    {
        $this->changePasswordInput["oldPassword"] = "unmatchPassword";
        
        $uri = $this->accountUri . "/change-password";
        $this->patch($uri, $this->changePasswordInput, $this->manager->token)
                ->seeStatusCode(403);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->manager->id,
            "name" => $this->manager->name,
            "email" => $this->manager->email,
            "phone" => $this->manager->phone,
        ];
        
        $this->get($this->accountUri, $this->manager->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
