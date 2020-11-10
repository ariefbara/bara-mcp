<?php

namespace Tests\Controllers\User\AsProgramParticipant;

use Tests\Controllers\ {
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfProgram,
    RecordPreparation\RecordOfFirm,
    RecordPreparation\RecordOfUser,
    RecordPreparation\User\RecordOfUserParticipant,
    User\UserTestCase
};

class AsProgramParticipantTestCase extends UserTestCase
{
    protected $asProgramparticipantUri;
    /**
     *
     * @var RecordOfUserParticipant
     */
    protected $programParticipation;
    /**
     *
     * @var RecordOfUserParticipant
     */
    protected $inactiveProgramParticipation;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Firm")->truncate();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        
        $userOne = new RecordOfUser(1);
        $this->connection->table("User")->insert($userOne->toArrayForDbEntry());
        
        $firm = new RecordOfFirm(999, "firm_0_identifier");
        $this->connection->table("Firm")->insert($firm->toArrayForDbEntry());
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 999);
        $inactiveParticipant = new RecordOfParticipant($program, 'inactive');
        $inactiveParticipant->active = false;
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($inactiveParticipant->toArrayForDbEntry());
        
        $this->programParticipation = new RecordOfUserParticipant($this->user, $participant);
        $this->inactiveProgramParticipation = new RecordOfUserParticipant($userOne, $inactiveParticipant);
        $this->connection->table("UserParticipant")->insert($this->programParticipation->toArrayForDbEntry());
        $this->connection->table("UserParticipant")->insert($this->inactiveProgramParticipation->toArrayForDbEntry());
        
        $this->asProgramparticipantUri = $this->userUri . "/as-program-participant/{$firm->id}/{$program->id}";
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
        $this->programParticipation->participant->active = false;
        $this->connection->table("Participant")->insert($this->programParticipation->participant->toArrayForDbEntry());
    }
}
