<?php

namespace Tests\Controllers\Admin;

use Tests\Controllers\RecordPreparation\RecordOfAdmin;

class AdminControllerTest extends AdminTestCase
{

    protected $adminOne, $adminTwo;
    protected $adminControllerUri;
    protected $addRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminOne = new RecordOfAdmin(1, 'admin_1@email.org', 'pwdAdmin1');
        $this->adminTwo = new RecordOfAdmin(2, 'admin_2@email.org', 'pwdAdmin2');

        $this->connection->table('Admin')->insert($this->adminOne->toArrayForDbEntry());
        $this->connection->table('Admin')->insert($this->adminTwo->toArrayForDbEntry());

        $this->adminControllerUri = $this->adminUri . "/admins";
        $this->addRequest = [
            "name" => 'new admin name',
            "email" => 'new_admin_address@email.org',
            "password" => 'newPassword123',
        ];
    }

    public function test_add()
    {
        $response = [
            "name" => $this->addRequest['name'],
            "email" => $this->addRequest['email'],
        ];
        $this->post($this->adminControllerUri, $this->addRequest, $this->admin->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);

        $adminRecord = [
            "name" => $this->addRequest['name'],
            "email" => $this->addRequest['email'],
            "removed" => false,
        ];
        $this->seeInDatabase("Admin", $adminRecord);
    }

    public function test_add_userNotAdmin_error403()
    {
        $this->post($this->adminControllerUri, $this->addRequest, $this->removedAdmin->token)
            ->seeStatusCode(401);
    }

    public function test_add_emailAlreadyRegistered_error409()
    {
        $this->addRequest['email'] = $this->adminOne->email;
        $this->post($this->adminControllerUri, $this->addRequest, $this->admin->token)
            ->seeStatusCode(409);
    }

    public function test_remove()
    {
        $uri = $this->adminControllerUri . "/{$this->adminOne->id}";
        $this->delete($uri, [], $this->admin->token)
            ->seeStatusCode(200);

        $adminRecord = [
            "id" => $this->adminOne->id,
            "removed" => true,
        ];
        $this->seeInDatabase('Admin', $adminRecord);
    }

    public function test_remove_userNotAdmin_error401()
    {
        $uri = $this->adminControllerUri . "/{$this->adminOne->id}";
        $this->delete($uri, [], $this->removedAdmin->token)
            ->seeStatusCode(401);
    }

    public function test_show()
    {
        $response = [
            "id" => $this->adminOne->id,
            "name" => $this->adminOne->name,
            "email" => $this->adminOne->email,
        ];
        $uri = $this->adminControllerUri . "/{$this->adminOne->id}";
        $this->get($uri, $this->admin->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }

    public function test_show_userNotAdmin_error401()
    {
        $uri = $this->adminControllerUri . "/{$this->adminOne->id}";
        $this->get($uri, $this->removedAdmin->token)
            ->seeStatusCode(401);
    }

    public function test_showAll()
    {
        $response = [
            "total" => 3,
            "list" => [
                ["id" => $this->admin->id, "name" => $this->admin->name],
                ["id" => $this->adminOne->id, "name" => $this->adminOne->name],
                ["id" => $this->adminTwo->id, "name" => $this->adminTwo->name],
            ],
        ];
        $this->get($this->adminControllerUri, $this->admin->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }

    public function test_showAll_userNotAdmin_error401()
    {
        $this->get($this->adminControllerUri, $this->removedAdmin->token)
            ->seeStatusCode(401);
    }

}
