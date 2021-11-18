<?php

namespace Tests\Controllers\Manager;

use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;

class ClientControllerTest extends EnhanceManagerTestCase
{
    protected $clientOne;
    protected $clientTwo;
    protected $showAllUri;
    protected $addRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Client")->truncate();
        
        $firm = $this->managerOne->firm;
        
        $this->clientOne = new RecordOfClient($firm, '1');
        $this->clientTwo = new RecordOfClient($firm, '2');
        
        $this->showAllUri = $this->managerUri . "/clients";
        
        $this->addRequest = [
            'firstName' => 'newfirstname',
            'lastName' => 'newlastname',
            'email' => 'newclient@email.org',
            'password' => 'newpassword123',
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Client")->truncate();
    }
    
    protected function add()
    {
        $this->insertPreparedManagerRecord();
        $uri = $this->managerUri . "/clients";
        $this->post($uri, $this->addRequest, $this->managerOne->token);
    }
    public function test_add_201()
    {
        $this->add();
        $this->seeStatusCode(201);
        
        $response = [
            "name" => $this->addRequest['firstName'] . ' ' . $this->addRequest['lastName'],
            "email" => $this->addRequest['email'],
            "activated" => true,
        ];
        $this->seeJsonContains($response);
        
        $clientRecord = [
            'Firm_id' => $this->managerOne->firm->id,
            "firstName" => $this->addRequest['firstName'],
            'lastName' => $this->addRequest['lastName'],
            "email" => $this->addRequest['email'],
            "activated" => true,
        ];
        $this->seeInDatabase('Client', $clientRecord);
    }
    public function test_add_emptyFirstName_400()
    {
        $this->addRequest['firstName'] = '';
        $this->add();
        $this->seeStatusCode(400);
    }
    public function test_add_invalidEmail_400()
    {
        $this->addRequest['email'] = 'invalid mail form';
        $this->add();
        $this->seeStatusCode(400);
    }
    public function test_add_invalidPasswordPatter_400()
    {
        $this->addRequest['password'] = 'invalidpattern';
        $this->add();
        $this->seeStatusCode(400);
    }
    
    protected function activate()
    {
        $this->insertPreparedManagerRecord();
        $this->clientOne->insert($this->connection);
        
        $uri = $this->managerUri . "/clients/{$this->clientOne->id}/activate";
        $this->patch($uri, [], $this->managerOne->token);
    }
    public function test_activate_200()
    {
        $this->clientOne->activated = false;
        $this->clientOne->activationCode = 'random-activation-token';
        $this->clientOne->activationCodeExpiredTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        
        $this->activate();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->clientOne->id,
            "name" => $this->clientOne->getFullName(),
            "email" => $this->clientOne->email,
            "signupTime" => $this->clientOne->signupTime,
            "activated" => true,
        ];
        $this->seeJsonContains($response);
        
        $record = [
            'id' => $this->clientOne->id,
            'activated' => true,
            'activationCode' => null,
            'activationCodeExpiredTime' => null,
        ];
        $this->seeInDatabase('Client', $record);
    }
    
    protected function show()
    {
        $this->insertPreparedManagerRecord();
        $this->clientOne->insert($this->connection);
        
        $uri = $this->managerUri . "/clients/{$this->clientOne->id}";
        $this->get($uri, $this->managerOne->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->clientOne->id,
            "name" => $this->clientOne->getFullName(),
            "email" => $this->clientOne->email,
            "signupTime" => $this->clientOne->signupTime,
            "activated" => $this->clientOne->activated,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->insertPreparedManagerRecord();
        
        $this->clientOne->insert($this->connection);
        $this->clientTwo->insert($this->connection);
        
        $this->get($this->showAllUri, $this->managerOne->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    "id" => $this->clientOne->id,
                    "name" => $this->clientOne->getFullName(),
                    "email" => $this->clientOne->email,
                    "signupTime" => $this->clientOne->signupTime,
                    "activated" => $this->clientOne->activated,
                ],
                [
                    "id" => $this->clientTwo->id,
                    "name" => $this->clientTwo->getFullName(),
                    "email" => $this->clientTwo->email,
                    "signupTime" => $this->clientTwo->signupTime,
                    "activated" => $this->clientTwo->activated,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_usingNameFilter_200()
    {
        $this->clientOne->firstName = 'Palmira';
        $this->clientOne->lastName = 'Rutski';
        $this->showAllUri .= "?name=uts";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    "id" => $this->clientOne->id,
                    "name" => $this->clientOne->getFullName(),
                    "email" => $this->clientOne->email,
                    "signupTime" => $this->clientOne->signupTime,
                    "activated" => $this->clientOne->activated,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_usingEmailFilter_200()
    {
        $this->clientOne->email = 'Rutski@email.org';
        $this->showAllUri .= "?email={$this->clientOne->email}";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    "id" => $this->clientOne->id,
                    "name" => $this->clientOne->getFullName(),
                    "email" => $this->clientOne->email,
                    "signupTime" => $this->clientOne->signupTime,
                    "activated" => $this->clientOne->activated,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_usingActivatedStatusFilter_200()
    {
        $this->clientOne->activated = false;
        $this->showAllUri .= "?activatedStatus=false";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    "id" => $this->clientOne->id,
                    "name" => $this->clientOne->getFullName(),
                    "email" => $this->clientOne->email,
                    "signupTime" => $this->clientOne->signupTime,
                    "activated" => $this->clientOne->activated,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
