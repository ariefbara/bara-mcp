<?php

namespace Tests\Controllers\RecordPreparation\Firm\Client;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfClientRegistrant implements Record
{

    /**
     *
     * @var RecordOfClient
     */
    public $client;

    /**
     *
     * @var RecordOfRegistrant
     */
    public $registrant;
    public $id;

    public function __construct(RecordOfClient $client, RecordOfRegistrant $registrant)
    {
        $this->client = $client;
        $this->registrant = $registrant;
        $this->id = $registrant->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Client_id' => $this->client->id,
            'Registrant_id' => $this->registrant->id,
            'id' => $this->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $this->registrant->insert($connection);
        $connection->table('ClientRegistrant')->insert($this->toArrayForDbEntry());
    }

}
