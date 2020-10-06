<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;

class TeamRegistrantTest extends TestBase
{
    protected $teamRegistrant;
    protected $program;
    protected $participantId = "participantId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamRegistrant = new TestableTeamRegistrant();
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    public function test_teamIdEquals_sameTeamId_returnTrue()
    {
        $this->assertTrue($this->teamRegistrant->teamIdEquals($this->teamRegistrant->teamId));
    }
    public function test_teamIdEquals_differentTeamId_returnFalse()
    {
        $this->assertFalse($this->teamRegistrant->teamIdEquals("differenet"));
    }
    
    public function test_createParticipant_returnParticipantForTeam()
    {
        $participant = Participant::participantForTeam($this->program, $this->participantId, $this->teamRegistrant->teamId);
        
        $this->assertEquals($participant, $this->teamRegistrant->createParticipant($this->program, $this->participantId));
    }
}

class TestableTeamRegistrant extends TeamRegistrant
{
    public $registrant;
    public $id;
    public $teamId = "teamId";
    
    function __construct()
    {
        parent::__construct();
    }
}
