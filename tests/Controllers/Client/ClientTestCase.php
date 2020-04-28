<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\ {
    ControllerTestCase,
    RecordPreparation\RecordOfClient
};

class ClientTestCase extends ControllerTestCase
{
    /**
     *
     * @var RecordOfClient
     */
    protected $client;
    protected $inactiveClient;
    protected $clientUri = "api/client";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        
        $this->client = new RecordOfClient('main', 'adi@barapraja.com', 'password123');
        $this->client->activated = true;
        $this->inactiveClient = new RecordOfClient('inactive', 'inactive_client@email.org', 'password123');
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());
        $this->connection->table('Client')->insert($this->inactiveClient->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
    }
}
