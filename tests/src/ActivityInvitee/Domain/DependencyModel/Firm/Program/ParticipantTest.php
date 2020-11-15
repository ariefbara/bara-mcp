<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm\Program;

use ActivityInvitee\Domain\DependencyModel\Firm\Team\ProgramParticipation;
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $participant;
    protected $teamParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant();
        $this->teamParticipant = $this->buildMockOfClass(ProgramParticipation::class);
        $this->participant->teamParticipant = $this->teamParticipant;
    }
    
    public function test_belongsToTeam_returnTeamParticipantTeamIdEqualsResult()
    {
        $this->teamParticipant->expects($this->once())
                ->method("teamIdEquals")
                ->with($teamId = "teamId");
        $this->participant->belongsToTeam($teamId);
    }
    public function test_belongsToTeam_notATeamParticipant_returnFalse()
    {
        $this->participant->teamParticipant = null;
        $this->assertFalse($this->participant->belongsToTeam("teamId"));
    }
}

class TestableParticipant extends Participant
{
    public $id;
    public $active;
    public $teamParticipant;
    
    function __construct()
    {
        parent::__construct();
    }
}
