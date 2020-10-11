<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\{
    ControllerTestCase,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\RecordOfFirm
};

class ClientTestCase extends ControllerTestCase
{

    /**
     *
     * @var RecordOfClient
     */
    protected $client;

    /**
     *
     * @var RecordOfClient
     */
    protected $inactiveClient;
    protected $clientUri = "api/client";

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();

        $firm = new RecordOfFirm(0, 'firm-identifier');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());

        $this->client = new RecordOfClient($firm, "main");
        $this->client->email = 'purnama.adi@gmail.com';
        $this->inactiveClient = new RecordOfClient($firm, 'inactive');
        $this->inactiveClient->activated = false;
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());
        $this->connection->table('Client')->insert($this->inactiveClient->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Client')->truncate();
    }

}
