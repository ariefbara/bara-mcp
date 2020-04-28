<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfProgram,
    RecordOfFirm
};

class ProgramParticipationTestCase extends ClientTestCase
{
    protected $programParticipationUri;
    /**
     *
     * @var RecordOfParticipant
     */
    protected $programParticipation;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->programParticipationUri = $this->clientUri . "/program-participations";
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        
        $firm = new RecordOfFirm(0, 'firm_identifier');
        $program = new RecordOfProgram($firm, 0);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $this->programParticipation = new RecordOfParticipant($program, $this->client, 0);
        $this->connection->table('Participant')->insert($this->programParticipation->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
    }
    
}
