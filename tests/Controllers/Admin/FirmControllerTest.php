<?php

namespace Tests\Controllers\Admin;

use Tests\Controllers\RecordPreparation\RecordOfFirm;

class FirmControllerTest extends AdminTestCase
{

    protected $firmOne, $firmTwo;
    protected $firmControllerUri;
    protected $addRequest = [
        "name" => 'new firm name',
        "identifier" => 'new_firm_identifier',
        "manager" => [
            "name" => 'new manager name',
            "email" => 'new_manager@email.org',
            "password" => 'password123',
            "phone" => '0812312353242',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->firmControllerUri = $this->adminUri . "/firms";
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();

        $this->firmOne = new RecordOfFirm(1, 'firm_one');
        $this->firmTwo = new RecordOfFirm(2, 'firm_two');
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Firm')->insert($this->firmOne->toArrayForDbEntry());
        $this->connection->table('Firm')->insert($this->firmTwo->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Manager')->truncate();
    }

    public function test_add()
    {
        $response = [
            "name" => $this->addRequest['name'],
            "identifier" => $this->addRequest['identifier'],
        ];

        $this->post($this->firmControllerUri, $this->addRequest, $this->admin->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $firmRecord = [
            "name" => $this->addRequest['name'],
            "identifier" => $this->addRequest['identifier'],
        ];
        $this->seeInDatabase('Firm', $firmRecord);
        $managerRecord = [
            "name" => $this->addRequest['manager']['name'],
            "email" => $this->addRequest['manager']['email'],
            "phone" => $this->addRequest['manager']['phone'],
        ];
        $this->seeInDatabase('Manager', $managerRecord);
//see Admin table manually to asserting that admin also created;
    }

    public function test_add_duplicateIdentifier_error409()
    {
        $this->addRequest['identifier'] = $this->firmOne->identifier;
        $this->post($this->firmControllerUri, $this->addRequest, $this->admin->token)
                ->seeStatusCode(409);
    }

    public function test_add_userNotAdmin_error401()
    {
        $this->post($this->firmControllerUri, $this->addRequest, $this->removedAdmin->token)
                ->seeStatusCode(401);
    }

    public function test_suspend()
    {
        $this->disableExceptionHandling();
        $uri = $this->firmControllerUri . "/{$this->firmOne->id}/suspend";
        $this->patch($uri, [], $this->admin->token)
                ->seeStatusCode(200);

        $firmRecord = [
            "id" => $this->firmOne->id,
            "suspended" => true,
        ];
        $this->seeInDatabase("Firm", $firmRecord);
    }

    public function test_suspend_userNotAdmin_error401()
    {
        $uri = $this->firmControllerUri . "/{$this->firmOne->id}/suspend";
        $this->patch($uri, [], $this->removedAdmin->token)
                ->seeStatusCode(401);
    }

    public function test_show()
    {
        $response = [
            "id" => $this->firmOne->id,
            "name" => $this->firmOne->name,
            "identifier" => $this->firmOne->identifier,
        ];
        $uri = $this->firmControllerUri . "/{$this->firmOne->id}";
        $this->get($uri, $this->admin->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_show_userNotAdmin_error401()
    {
        $uri = $this->firmControllerUri . "/{$this->firmOne->id}";
        $this->get($uri, $this->removedAdmin->token)
                ->seeStatusCode(401);
    }

    public function test_showAll()
    {
        $this->disableExceptionHandling();
        $response = [
            "total" => 2,
            "list" => [
                ["id" => $this->firmOne->id, "name" => $this->firmOne->name],
                ["id" => $this->firmTwo->id, "name" => $this->firmTwo->name],
            ],
        ];
        $this->get($this->firmControllerUri, $this->admin->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_showAll_userNotAdmin_error401()
    {
        $this->get($this->firmControllerUri, $this->removedAdmin->token)
                ->seeStatusCode(401);
    }

}
