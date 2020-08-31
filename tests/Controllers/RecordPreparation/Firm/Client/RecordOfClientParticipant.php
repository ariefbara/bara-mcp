<?php

namespace Tests\Controllers\RecordPreparation\Firm\Client;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Record
};

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

}
