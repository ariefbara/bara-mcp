<?php

namespace Firm\Domain\Model\Firm\Program;

use Tests\TestBase;

class TeamParticipantTest extends TestBase
{
    protected $participant;
    protected $teamParticipant;
    protected $id = "newId", $teamId = "newTeamId";
    protected $registrant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->teamParticipant = new TestableTeamParticipant($this->participant, "id", "teamId");
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
    }
    
    public function test_construct_setProperties()
    {
        $teamParticipant = new TestableTeamParticipant($this->participant, $this->id, $this->teamId);
        $this->assertEquals($this->participant, $teamParticipant->participant);
        $this->assertEquals($this->id, $teamParticipant->id);
        $this->assertEquals($this->teamId, $teamParticipant->teamId);
    }
    
    public function test_correspondWithRegistrant_returnRegistrantCorrespondWithTeamResult()
    {
        $this->registrant->expects($this->once())
                ->method("correspondWithTeam")
                ->with($this->teamParticipant->teamId);
        $this->teamParticipant->correspondWithRegistrant($this->registrant);
    }
}

class TestableTeamParticipant extends TeamParticipant
{
    public $participant;
    public $id;
    public $teamId;
}
