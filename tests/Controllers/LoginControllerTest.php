<?php

namespace Tests\Controllers;

use Tests\Controllers\RecordPreparation\ {
    Firm\RecordOfManager,
    RecordOfAdmin,
    RecordOfFirm
};

class LoginControllerTest extends ControllerTestCase
{
    protected $admin;
    protected $adminLoginUri = "/api/admin-login";
    protected $adminLoginRequest;
    
    protected $firm;
    protected $manager;
    protected $managerLoginUri = "/api/manager-login";
    protected $managerLoginRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Admin')->truncate();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
        
        $this->admin = new RecordOfAdmin('admin', 'sys_admin@email.org', 'password123');
        $this->connection->table('Admin')->insert($this->admin->toArrayForDbEntry());
        $this->adminLoginRequest = [
            "email" => $this->admin->email,
            "password" => $this->admin->rawPassword,
        ];
        
        $this->firm = new RecordOfFirm(0, 'identifier');
        $this->connection->table('Firm')->insert($this->firm->toArrayForDbEntry());
        
        $this->manager = new RecordOfManager($this->firm, 0, 'manager@email.org', 'password123');
        $this->connection->table('Manager')->insert($this->manager->toArrayForDbEntry());
        $this->managerLoginRequest = [
            "firmIdentifier" => $this->firm->identifier,
            "email" => $this->manager->email,
            "password" => $this->manager->rawPassword,
        ];
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Admin')->truncate();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
    }
    
    public function test_adminLogin()
    {
        $response = [
            "id" => $this->admin->id,
            "name" => $this->admin->name,
        ];
        $this->post($this->adminLoginUri, $this->adminLoginRequest)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
//use insomnia to see if response contain JWT credentials
    }
    public function test_adminLogin_nonExistingEmail_error401()
    {
        $this->adminLoginRequest['email'] = 'non_existing_address@email.org';
        $this->post($this->adminLoginUri, $this->adminLoginRequest)
            ->seeStatusCode(401);
    }
    public function test_adminLogin_unmatchedPassword_error401()
    {
        $this->adminLoginRequest['password'] = 'unmatched_password';
        $this->post($this->adminLoginUri, $this->adminLoginRequest)
            ->seeStatusCode(401);
    }
    
    public function test_managerLogin()
    {
        $response = [
            "id" => $this->manager->id,
            "name" => $this->manager->name,
        ];
        $this->post($this->managerLoginUri, $this->managerLoginRequest)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
//use insomnia to see if response contain JWT credentials
    }
    public function test_managerLogin_nonExistingEmail_error401()
    {
        $this->managerLoginRequest['email'] = 'non_existing_address@email.org';
        $this->post($this->managerLoginUri, $this->managerLoginRequest)
            ->seeStatusCode(401);
    }
    public function test_managerLogin_unmatchedPassword_error401()
    {
        $this->managerLoginRequest['password'] = 'unmatched_password';
        $this->post($this->managerLoginUri, $this->managerLoginRequest)
            ->seeStatusCode(401);
    }
}
