<?php

namespace Tests\Controllers\User;

use Tests\Controllers\RecordPreparation\ {
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfProgram,
    RecordOfFirm,
    User\RecordOfUserParticipant
};

class ProgramParticipationTestCase extends UserTestCase
{
    protected $programParticipationUri;
    
    /**
     *
     * @var RecordOfUserParticipant
     */
    protected $programParticipation;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipationUri = $this->userUri . "/program-participations";
        
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Participant')->truncate();
        
        $firm = new RecordOfFirm(999, 'firm-999-identifier');
        $this->connection->table('Firm')->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 999);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());
        
        $this->programParticipation = new RecordOfUserParticipant($this->user, $participant);
        $this->connection->table('UserParticipant')->insert($this->programParticipation->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('Participant')->truncate();
    }
    
    protected function setParticipantInactive()
    {
        $this->connection->table("Participant")->truncate();
        $this->programParticipation->participant->active = false;
        $this->connection->table("Participant")->insert($this->programParticipation->participant->toArrayForDbEntry());
    }
}
