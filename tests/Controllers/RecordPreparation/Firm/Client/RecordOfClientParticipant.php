<?php

namespace Tests\Controllers\RecordPreparation\Firm\Client;

use Illuminate\Database\ConnectionInterface;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Record;

class RecordOfClientParticipant implements Record
{

    /**
     *
     * @var RecordOfClient
     */
    public $client;

    /**
     *
     * @var RecordOfParticipant
     */
    public $participant;
    public $id;

    public function __construct(RecordOfClient $client, RecordOfParticipant $participant)
    {
        $this->client = $client;
        $this->participant = $participant;
        $this->id = $participant->id;
    }

    public function toArrayForDbEntry()
    {
        return [
            'Client_id' => $this->client->id,
            'Participant_id' => $this->participant->id,
            'id' => $this->id,
        ];
    }
    
    public function insert(ConnectionInterface $connection): void
    {
        $connection->table('ClientParticipant')->insert($this->toArrayForDbEntry());
    }

}
