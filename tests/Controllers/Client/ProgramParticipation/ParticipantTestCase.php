<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\Client\ClientTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ParticipantTestCase extends ClientTestCase
{
    /**
     * 
     * @var RecordOfClientParticipant
     */
    protected $clientParticipant;
    
    protected $clientParticipantUri;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        
        $firm = $this->client->firm;
        
        $program = new RecordOfProgram($firm, '99');
        $participant = new RecordOfParticipant($program, '99');
        $this->clientParticipant = new RecordOfClientParticipant($this->client, $participant);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
    }
}
