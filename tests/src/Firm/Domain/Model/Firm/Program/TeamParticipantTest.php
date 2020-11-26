<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Program\MeetingType\MeetingData,
    Team
};
use Tests\TestBase;

class TeamParticipantTest extends TestBase
{
    protected $participant;
    protected $teamParticipant;
    protected $id = "newId", $teamId = "newTeamId";
    protected $registrant;
    protected $team;
    protected $meetingId = "meetingId", $meetingType, $meetingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->teamParticipant = new TestableTeamParticipant($this->participant, "id", "teamId");
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        
        $this->team = $this->buildMockOfClass(Team::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
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
    
    public function test_belongsToTeam_returnTeamsIdEqualsResult()
    {
        $this->team->expects($this->once())
                ->method("idEquals")
                ->with($this->teamParticipant->teamId);
        $this->teamParticipant->belongsToTeam($this->team);
    }
    
    public function test_initiateMeeting_returnParticipantsInitiateMeetingResult()
    {
        $this->participant->expects($this->once())
                ->method("initiateMeeting")
                ->with($this->meetingId, $this->meetingType, $this->meetingData);
        $this->teamParticipant->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
}

class TestableTeamParticipant extends TeamParticipant
{
    public $participant;
    public $id = "id";
    public $teamId = "teamId";
}
