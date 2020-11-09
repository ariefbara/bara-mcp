<?php

namespace Tests\Controllers\Client\AsTeamMember;

use Tests\Controllers\RecordPreparation\Firm\ {
    Program\RecordOfParticipant,
    RecordOfProgram,
    Team\RecordOfTeamProgramParticipation
};

class ProgramParticipationTestCase extends AsTeamMemberTestCase
{

    protected $programParticipationUri;

    /**
     *
     * @var RecordOfTeamProgramParticipation
     */
    protected $programParticipation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->programParticipationUri = $this->asTeamMemberUri . "/program-participations";

        $this->connection->table('Program')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Participant')->truncate();

        $program = new RecordOfProgram($this->client->firm, 999);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());

        $participant = new RecordOfParticipant($program, 999);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $team = $this->teamMember->team;
        $this->programParticipation = new RecordOfTeamProgramParticipation($team, $participant);
        $this->connection->table('TeamParticipant')->insert($this->programParticipation->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Participant')->truncate();
    }
    
    protected function setInactiveParticipant()
    {
        $this->connection->table("Participant")->truncate();
        $this->programParticipation->participant->active = false;
        $this->connection->table("Participant")->insert($this->programParticipation->participant->toArrayForDbEntry());
    }

}
