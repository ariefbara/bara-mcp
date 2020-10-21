<?php

namespace Tests\Controllers\Manager;

use DateTime;
use Tests\Controllers\ {
    Manager\ManagerTestCase,
    RecordPreparation\Firm\RecordOfPersonnel
};

class PersonnelControllerTest extends ManagerTestCase
{
    protected $uri;
    protected $personnel, $personnelOne;
    protected $personnelInput = [
        "firstName" => 'firstname',
        "lastName" => 'lastname',
        "email" => 'new_personnel@email.org',
        "password" => 'password213',
        "phone" => '081231231231',
        "bio" => 'new personnel bio',
    ];


    protected function setUp(): void
    {
        parent::setUp();
        $this->uri = $this->managerUri . "/personnels";
        $this->connection->table('Personnel')->truncate();
        
        $this->personnel = new RecordOfPersonnel($this->firm, 0, 'personnel@email.org', 'password123');
        $this->personnelOne = new RecordOfPersonnel($this->firm, 1, 'personnel_one@email.org', 'password123');
        $this->connection->table('Personnel')->insert($this->personnel->toArrayForDbEntry());
        $this->connection->table('Personnel')->insert($this->personnelOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
    }
    
    public function test_add()
    {
        $response = [
            "name" => $this->personnelInput['firstName'] . " " . $this->personnelInput['lastName'],
            "email" => $this->personnelInput['email'],
            "phone" => $this->personnelInput['phone'],
            "bio" => $this->personnelInput['bio'],
            "joinTime" => (new DateTime())->format('Y-m-d H:i:s'),
        ];
        $this->post($this->uri, $this->personnelInput, $this->manager->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $personnelRecord = [
            "Firm_id" => $this->firm->id,
            "firstName" => $this->personnelInput['firstName'],
            "lastName" => $this->personnelInput['lastName'],
            "email" => $this->personnelInput['email'],
            "phone" => $this->personnelInput['phone'],
            "bio" => $this->personnelInput['bio'],
            "joinTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "removed" => false,
        ];
        $this->seeInDatabase('Personnel', $personnelRecord);
    }
    public function test_add_emptyName_error400()
    {
        $this->personnelInput['firstName'] = '';
        $this->post($this->uri, $this->personnelInput, $this->manager->token)
            ->seeStatusCode(400);
    }
    public function test_add_invalidEmailFormat_error400()
    {
        $this->personnelInput['email'] = 'bad-format';
        $this->post($this->uri, $this->personnelInput, $this->manager->token)
            ->seeStatusCode(400);
    }
    public function test_add_duplicateEmail_error409()
    {
        $this->personnelInput['email'] = $this->personnel->email;
        $this->post($this->uri, $this->personnelInput, $this->manager->token)
            ->seeStatusCode(409);
    }
    public function test_add_userNotManager_error401()
    {
        $this->post($this->uri, $this->personnelInput, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->personnel->id,
            "name" => $this->personnel->getFullName(),
            "email" => $this->personnel->email,
            "phone" => $this->personnel->phone,
            "bio" => $this->personnel->bio,
            "joinTime" => $this->personnel->joinTime,
        ];
        $uri = $this->uri . "/{$this->personnel->id}";
        $this->get($uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_userNotManager_error401()
    {
        $uri = $this->uri . "/{$this->personnel->id}";
        $this->get($uri, $this->removedManager->token)
            ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->personnel->id,
                    "name" => $this->personnel->getFullName(),
                ],
                [
                    "id" => $this->personnelOne->id,
                    "name" => $this->personnelOne->getFullName(),
                ],
            ],
        ];
        $this->get($this->uri, $this->manager->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_userNotManager_error401()
    {
        $this->get($this->uri, $this->removedManager->token)
            ->seeStatusCode(401);
        
    }
}
