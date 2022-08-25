<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\ControllerTestCase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\RecordOfFirm;

class ExtendedClientControllerTestCase extends ControllerTestCase
{
    /**
     * 
     * @var RecordOfClient
     */
    protected $client;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
        
        $firm = new RecordOfFirm('0');
        $this->client = new RecordOfClient($firm, '0');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
    }
    
    protected function persistClientDependency()
    {
        $this->client->firm->insert($this->connection);
        $this->client->insert($this->connection);
    }
}
