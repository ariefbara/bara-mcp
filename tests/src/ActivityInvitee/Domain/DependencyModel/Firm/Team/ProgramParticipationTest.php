<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Team;

use Tests\TestBase;

class ProgramParticipationTest extends TestBase
{
    protected $programParticipation;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipation = new TestableProgramParticipation();
    }
    
    public function test_teamIdEquals_sameTeamId_returnTrue()
    {
        $this->assertTrue($this->programParticipation->teamIdEquals($this->programParticipation->teamId));
    }
    public function test_teamIdEquals_differentTeamId_returnFalse()
    {
        $this->assertFalse($this->programParticipation->teamIdEquals("different teamId"));
    }
}

class TestableProgramParticipation extends ProgramParticipation
{
    public $teamId = "teamId";
    public $id;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
