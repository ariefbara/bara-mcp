<?php

namespace Tests\Controllers;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfCoordinator,
    Firm\RecordOfClient,
    Firm\RecordOfManager,
    Firm\RecordOfPersonnel,
    Firm\RecordOfProgram,
    RecordOfAdmin,
    RecordOfFirm,
    RecordOfUser
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
    
    protected $client, $inactiveClient;
    protected $clientLoginUri = "/api/client-login";
    protected $clientLoginRequest;
    protected $personnel, $removedPersonnel;
    
    protected $personnelLoginUri = "/api/personnel-login";
    protected $personnelLoginRequest;
    protected $coordinator, $consultant;
    
    protected $user, $inactiveUser;
    protected $userLoginUri = "/api/user-login";
    protected $userLoginRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Admin')->truncate();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('User')->truncate();

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

        $this->client = new RecordOfClient($this->firm, 0);
        $this->inactiveClient = new RecordOfClient($this->firm, 1);
        $this->inactiveClient->activated = false;
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());
        $this->connection->table('Client')->insert($this->inactiveClient->toArrayForDbEntry());
        $this->clientLoginRequest = [
            "firmIdentifier" => $this->firm->identifier,
            "email" => $this->client->email,
            "password" => $this->client->rawPassword,
        ];

        $this->personnel = new RecordOfPersonnel($this->firm, 0, 'personnel@email.org', 'password123');
        $this->removedPersonnel = new RecordOfPersonnel($this->firm, 1, 'removed_personnel@email.org', 'password123');
        $this->removedPersonnel->removed = true;
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->removedPersonnel->toArrayForDbEntry());

        $program = new RecordOfProgram($this->firm, 0);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());

        $this->consultant = new RecordOfConsultant($program, $this->personnel, 0);
        $this->connection->table('Consultant')->insert($this->consultant->toArrayForDbEntry());

        $this->coordinator = new RecordOfCoordinator($program, $this->personnel, 0);
        $this->connection->table('Coordinator')->insert($this->coordinator->toArrayForDbEntry());
        
        $this->personnelLoginRequest = [
            "firmIdentifier" => $this->firm->identifier,
            "email" => $this->personnel->email,
            "password" => $this->personnel->rawPassword,
        ];
        
        $this->user = new RecordOfUser(0);
        $this->inactiveUser = new RecordOfUser(1);
        $this->inactiveUser->activated = false;
        $this->connection->table('User')->insert($this->user->toArrayForDbEntry());
        $this->connection->table('User')->insert($this->inactiveUser->toArrayForDbEntry());
        $this->userLoginRequest = [
            'email' => $this->user->email,
            'password' => $this->user->rawPassword,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Admin')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('User')->truncate();
    }

    public function test_adminLogin()
    {
        $response = [
            "id" => $this->admin->id,
            "name" => $this->admin->name
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

    public function test_clientLogin()
    {
        $response = [
            "id" => $this->client->id,
            "name" => $this->client->firstName . " " . $this->client->lastName,
        ];
        $this->post($this->clientLoginUri, $this->clientLoginRequest)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
//use insomnia to see if response contain JWT credentials
    }

    public function test_clientLogin_nonExistingEmail_error401()
    {
        $this->clientLoginRequest['email'] = 'non_existing_address@email.org';
        $this->post($this->clientLoginUri, $this->clientLoginRequest)
                ->seeStatusCode(401);
    }

    public function test_clientLogin_unmatchedPassword_error401()
    {
        $this->clientLoginRequest['password'] = 'unmatched_password';
        $this->post($this->clientLoginUri, $this->clientLoginRequest)
                ->seeStatusCode(401);
    }

    public function test_clientLogin_inactiveClient_error401()
    {
        $request = [
            "firmIdentifier" => $this->inactiveClient->firm->identifier,
            "email" => $this->inactiveClient->email,
            "password" => $this->inactiveClient->rawPassword,
        ];
        $this->post($this->clientLoginUri, $request)
                ->seeStatusCode(401);
    }
    
    public function test_personnelLogin()
    {
        $response = [
            "id" => $this->personnel->id,
            "name" => $this->personnel->getFullName(),
            "programConsultants" => [
                [
                    "id" => $this->consultant->id,
                    "program" => [
                        "id" => $this->consultant->program->id,
                        "name" => $this->consultant->program->name,
                    ],
                ],
            ],
            "programCoordinators" => [
                [
                    "id" => $this->coordinator->id,
                    "program" => [
                        "id" => $this->coordinator->program->id,
                        "name" => $this->coordinator->program->name,
                    ],
                ],
            ],
        ];
        $this->post($this->personnelLoginUri, $this->personnelLoginRequest)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_personnelLogin_personnelNotExist_error401()
    {
        $this->personnelLoginRequest['email'] = 'nonExistPersonnel@email.org';
        $this->post($this->personnelLoginUri, $this->personnelLoginRequest)
                ->seeStatusCode(401);
    }
    public function test_personnelLogin_unmatchedPassword_error401()
    {
        $this->personnelLoginRequest['password'] = 'unmatched';
        $this->post($this->personnelLoginUri, $this->personnelLoginRequest)
                ->seeStatusCode(401);
    }
    public function test_personnelLogin_removedPersonnel_error401()
    {
        $loginRequest = [
            "firmIdentifier" => $this->firm->identifier,
            "email" => $this->removedPersonnel->email,
            "password" => $this->removedPersonnel->rawPassword,
        ];
        $this->post($this->personnelLoginUri, $loginRequest)
                ->seeStatusCode(401);
    }
    
    public function test_userLogin()
    {
        $response = [
            "id" => $this->user->id,
            "name" => $this->user->firstName . " " . $this->user->lastName,
        ];
        $this->post($this->userLoginUri, $this->userLoginRequest)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_userLogin_inactiveAccount_401()
    {
        $this->userLoginRequest['email'] = $this->inactiveUser->email;
        $this->post($this->userLoginUri, $this->userLoginRequest)
                ->seeStatusCode(401);
    }
    public function test_userLogin_emailNotFound_401()
    {
        $this->userLoginRequest['email'] = 'non-existing@email.org';
        $this->post($this->userLoginUri, $this->userLoginRequest)
                ->seeStatusCode(401);
    }
    public function test_userLogin_unmatchPassword_401()
    {
        $this->userLoginRequest['password'] = 'unmatch123';
        $this->post($this->userLoginUri, $this->userLoginRequest)
                ->seeStatusCode(401);
    }

}
