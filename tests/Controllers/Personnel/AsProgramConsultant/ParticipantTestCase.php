<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    RecordOfClient
};

class ParticipantTestCase extends AsProgramConsultantTestCase
{
    protected $participantUri;
    /**
     *
     * @var RecordOfParticipant
     */
    protected $participant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantUri = $this->asProgramConsultantUri . "/participants";
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        
        $client = new RecordOfClient(0, 'adi@barapraja.com', 'password123');
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());
        
        $this->participant = new RecordOfParticipant($this->consultant->program, $client, 0);
        $this->connection->table('Participant')->insert($this->participant->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
    }
}
