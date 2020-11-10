<?php

namespace Tests\Controllers\User\AsFirmParticipant;

use Tests\Controllers\ {
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfProgram,
    RecordPreparation\RecordOfFirm,
    RecordPreparation\User\RecordOfUserParticipant,
    User\UserTestCase
};

class AsFirmParticipantTestCase extends UserTestCase
{
    protected $asFirmParticipantUri;
    /**
     *
     * @var RecordOfUserParticipant
     */
    protected $programParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        
        $firm = new RecordOfFirm(999);
        $this->connection->table("Firm")->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 999);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->programParticipant = new RecordOfUserParticipant($this->user, $participant);
        $this->connection->table("UserParticipant")->insert($this->programParticipant->toArrayForDbEntry());
        
        $this->asFirmParticipantUri = $this->userUri . "/as-firm-participant/{$firm->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
    }
    
    protected function setInactiveParticipant()
    {
        $this->connection->table("Participant")->truncate();
        $this->programParticipant->participant->active = false;
        $this->connection->table("Participant")->insert($this->programParticipant->participant->toArrayForDbEntry());
    }
}
