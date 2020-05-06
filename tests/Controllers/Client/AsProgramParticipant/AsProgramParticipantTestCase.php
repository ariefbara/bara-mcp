<?php

namespace Tests\Controllers\Client\AsProgramParticipant;

use Tests\Controllers\ {
    Client\ClientTestCase,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfProgram,
    RecordPreparation\RecordOfClient,
    RecordPreparation\RecordOfFirm
};

class AsProgramParticipantTestCase extends ClientTestCase
{
    protected $asProgramparticipantUri;
    /**
     *
     * @var RecordOfParticipant
     */
    protected $participant;
    /**
     *
     * @var RecordOfParticipant
     */
    protected $inactiveParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        
        $firm = new RecordOfFirm(0, 'firm_identifier');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 0);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $client = new RecordOfClient('1', 'inactiveParticipant@email.org', 'random123');
        $client->activated = true;
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());
        
        $this->participant = new RecordOfParticipant($program, $this->client, 0);
        $this->inactiveParticipant = new RecordOfParticipant($program, $client, 'inactive');
        $this->inactiveParticipant->active = false;
        $this->connection->table('Participant')->insert($this->participant->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($this->inactiveParticipant->toArrayForDbEntry());
        
        $this->asProgramparticipantUri = $this->clientUri . "/as-program-participant/{$firm->id}/{$program->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
    }
}
