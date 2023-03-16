<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\Client\ClientTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class ExtendedClientParticipantTestCase extends ClientTestCase
{

    /**
     * 
     * @var RecordOfClientParticipant
     */
    protected $clientParticipant;
    protected $clientParticipantUri;
    
    /**
     * 
     * @var RecordOfParticipant
     */
    protected $otherParticipant;
    /**
     * 
     * @var RecordOfProgram
     */
    protected $otherProgram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        
        $firm = $this->client->firm;
        $program = new RecordOfProgram($firm, '999');
        $this->otherProgram = new RecordOfProgram($firm, 'other');
        
        $this->otherParticipant = new RecordOfParticipant($program, 'other');
        $participant = new RecordOfParticipant($program, '999');
        $this->clientParticipant = new RecordOfClientParticipant($this->client, $participant);
        
        $this->clientParticipantUri = $this->clientUri . "/program-participations/{$this->clientParticipant->id}";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
    }
    
    protected function insertClientParticipantRecord()
    {
        $this->clientParticipant->participant->program->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
    }

}
