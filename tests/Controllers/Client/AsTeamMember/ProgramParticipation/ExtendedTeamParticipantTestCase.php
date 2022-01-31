<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\Client\AsTeamMember\ExtendedTeamMemberTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class ExtendedTeamParticipantTestCase extends ExtendedTeamMemberTestCase
{
    /**
     * 
     * @var RecordOfTeamProgramParticipation
     */
    protected $teamParticipant;
    protected $teamParticipantUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        
        $firm = $this->teamMember->team->firm;
        
        $program = new RecordOfProgram($firm, '999');
        $participant = new RecordOfParticipant($program, '999');
        $this->teamParticipant = new RecordOfTeamProgramParticipation($this->teamMember->team, $participant);
        
        $this->teamParticipantUri = $this->asTeamMemberUri . "/program-participations/{$this->teamParticipant->id}";
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
    }

    protected function prepareRecord(): void
    {
        parent::prepareRecord();
        $this->teamParticipant->participant->program->insert($this->connection);
        $this->teamParticipant->insert($this->connection);
    }

}
