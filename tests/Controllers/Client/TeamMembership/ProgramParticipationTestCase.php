<?php

namespace Tests\Controllers\Client\TeamMembership;

use Tests\Controllers\ {
    Client\TeamMembershipTestCase,
    RecordPreparation\Firm\Client\RecordOfClientParticipant,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfProgram,
    RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation
};

class ProgramParticipationTestCase extends TeamMembershipTestCase
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

        $this->programParticipationUri = $this->teamMembershipUri . "/program-participations";

        $this->connection->table('Program')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Participant')->truncate();

        $program = new RecordOfProgram($this->client->firm, 0);
        $this->connection->table('Program')->insert($program->toArrayForDbEntry());

        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $team = $this->teamMembership->team;
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

}
