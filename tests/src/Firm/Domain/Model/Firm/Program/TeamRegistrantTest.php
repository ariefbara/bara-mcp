<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Team;
use Tests\TestBase;

class TeamRegistrantTest extends TestBase
{
    protected $teamRegistrant, $team;
    protected $program;
    protected $participantId = "participantId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRegistrant = new TestableTeamRegistrant();
        $this->team = $this->buildMockOfClass(Team::class);
        $this->teamRegistrant->team = $this->team;
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    protected function teamEquals()
    {
        return $this->teamRegistrant->teamEquals($this->team);
    }
    public function test_teamEquals_sameTeam_returnTrue()
    {
        $this->assertTrue($this->teamEquals());
    }
    public function test_teamEquals_differentTeam_returnFalse()
    {
        $this->teamRegistrant->team = $this->buildMockOfClass(Team::class);
        $this->assertFalse($this->teamEquals());
    }
    
    public function test_createParticipant_returnParticipantForTeam()
    {
        $participant = Participant::participantForTeam($this->program, $this->participantId, $this->team);
        
        $this->assertEquals($participant, $this->teamRegistrant->createParticipant($this->program, $this->participantId));
    }
}

class TestableTeamRegistrant extends TeamRegistrant
{
    public $registrant;
    public $id;
    public $team;
    
    function __construct()
    {
        parent::__construct();
    }
}
