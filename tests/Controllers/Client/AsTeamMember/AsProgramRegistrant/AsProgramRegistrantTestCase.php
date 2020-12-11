<?php

namespace Tests\Controllers\Client\AsTeamMember\AsProgramRegistrant;

use Tests\Controllers\Client\AsTeamMember\AsTeamMemberTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration;

class AsProgramRegistrantTestCase extends AsTeamMemberTestCase
{
    protected $asProgramRegistrantUri;
    /**
     * 
     * @var RecordOfTeamRegistrant
     */
    protected $programRegistration;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("TeamRegistrant")->truncate();
        
        $team = $this->teamMember->team;
        $firm = $team->firm;
        
        $program = new RecordOfProgram($firm, 999);
        $this->connection->table("Program")->insert($program->toArrayForDbEntry());
        
        $registrant = new RecordOfRegistrant($program, 999);
        $this->connection->table("Registrant")->insert($registrant->toArrayForDbEntry());
        
        $this->programRegistration = new RecordOfTeamProgramRegistration($team, $registrant);
        $this->connection->table("TeamRegistrant")->insert($this->programRegistration->toArrayForDbEntry());
        
        $this->asProgramRegistrantUri = $this->asTeamMemberUri . "/as-program-registrant/{$program->id}";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("TeamRegistrant")->truncate();
    }
    
    protected function setRegistrationConcluded()
    {
        $this->programRegistration->registrant->concluded = true;
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("Registrant")->insert($this->programRegistration->registrant->toArrayForDbEntry());
    }
}
