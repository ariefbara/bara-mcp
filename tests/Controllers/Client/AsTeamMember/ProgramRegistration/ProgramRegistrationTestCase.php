<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramRegistration;

use Tests\Controllers\Client\AsTeamMember\AsTeamMemberTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration;

class ProgramRegistrationTestCase extends AsTeamMemberTestCase
{

    protected $programRegistrationUri;

    /**
     * 
     * @var RecordOfTeamProgramRegistration
     */
    protected $programRegistration;

    /**
     * 
     * @var RecordOfTeamProgramRegistration
     */
    protected $concludedProgramRegistration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->programRegistrationUri = $this->asTeamMemberUri . "/program-registrations";

        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("TeamRegistrant")->truncate();

        $team = $this->teamMember->team;
        $firm = $team->firm;

        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());

        $registrant = new RecordOfRegistrant($program, 999);
        $concludedRegistrant = new RecordOfRegistrant($program, 888);
        $concludedRegistrant->concluded = true;
        $this->connection->table("Registrant")->insert($registrant->toArrayForDbEntry());
        $this->connection->table("Registrant")->insert($concludedRegistrant->toArrayForDbEntry());

        $this->programRegistration = new RecordOfTeamProgramRegistration($team, $registrant);
        $this->concludedProgramRegistration = new RecordOfTeamProgramRegistration($team, $concludedRegistrant);
        $this->connection->table("TeamRegistrant")->insert($this->programRegistration->toArrayForDbEntry());
        $this->connection->table("TeamRegistrant")->insert($this->concludedProgramRegistration->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("TeamRegistrant")->truncate();
    }

}
