<?php

namespace Tests\Controllers;

use Tests\Controllers\RecordPreparation\RecordOfClient;

class SignupControllerTest extends ControllerTestCase
{
    protected $client;
    
    protected $clientSignupUri = "/api/client-signup";
    protected $clientSignupInput;


    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        
        $this->client = new RecordOfClient('client', 'adi@barapraja.com', 'password123');
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());
        
        $this->clientSignupInput = [
            "name" => 'new client',
            "email" => 'purnama.adi@gmail.com',
            "password" => 'newPwd123',
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
    }
    
    public function test_clientSignup()
    {
$this->disableExceptionHandling();
//use valid mail to check if activation mail sent
        $response = [
            "meta" => [
                "code" => 201,
                "type" => "Created",
            ],
        ];
        $this->post($this->clientSignupUri, $this->clientSignupInput)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $clientRecord = [
            "name" => $this->clientSignupInput['name'],
            "email" => $this->clientSignupInput['email'],
            "activated" => false,
        ];
        $this->seeInDatabase('Client', $clientRecord);
    }
    public function test_clientSignup_emailAlreadyRegistered_error409()
    {
        $this->clientSignupInput['email'] = $this->client->email;
        $this->post($this->clientSignupUri, $this->clientSignupInput)
            ->seeStatusCode(409);
    }
}
