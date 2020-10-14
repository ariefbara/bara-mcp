<?php

namespace Tests\Controllers\Client\AsTeamMember\AsProgramParticipant;

use Tests\Controllers\ {
    Client\AsTeamMember\AsTeamMemberTestCase,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfProgram,
    RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation
};

class AsProgramParticipantTestCase extends AsTeamMemberTestCase
{
    protected $asProgramParticipantUri;
    /**
     *
     * @var RecordOfTeamProgramParticipation
     */
    protected $programParticipant;
    /**
     *
     * @var RecordOfTeamProgramParticipation
     */
    protected $programParticipantOne_inactive;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        
        $team = $this->teamMember->team;
        $firm = $team->firm;
        
        $program = new RecordOfProgram($firm, 0);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->programParticipant = new RecordOfTeamProgramParticipation($team, $participant);
        $this->connection->table("TeamParticipant")->insert($this->programParticipant->toArrayForDbEntry());
        
        $this->asProgramParticipantUri = $this->asTeamMemberUri . "/as-program-participant/{$program->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
    }
    
    protected function setInactiveParticipant()
    {
        $this->connection->table("Participant")->truncate();
        $this->programParticipant->participant->active = false;
        $this->connection->table("Participant")->insert($this->programParticipant->participant->toArrayForDbEntry());
    }
}
