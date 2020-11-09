<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\Participant,
    service\ActivityDataProvider
};
use Tests\TestBase;

class ParticipantActivityTest extends TestBase
{
    protected $participant;
    protected $activity;
    protected $participantActivity;
    protected $id = "newId";
    protected $activityDataProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->activity = $this->buildMockOfClass(Activity::class);
        $this->participantActivity = new TestableParticipantActivity($this->participant, "id", $this->activity);
        
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
    }
    
    public function test_construct_setProperties()
    {
        $participantActivity = new TestableParticipantActivity($this->participant, $this->id, $this->activity);
        $this->assertEquals($this->participant, $participantActivity->participant);
        $this->assertEquals($this->id, $participantActivity->id);
        $this->assertEquals($this->activity, $participantActivity->activity);
    }
    
    public function test_update_updateActivity()
    {
        $this->activity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->participantActivity->update($this->activityDataProvider);
    }
    
    public function test_belongsToTeam_returnParticipantBelongsToTeamResult()
    {
        $this->participant->expects($this->once())
                ->method("belongsToTeam")
                ->with($teamId = "teamId");
        $this->participantActivity->belongsToTeam($teamId);
    }
}

class TestableParticipantActivity extends ParticipantActivity
{
    public $participant;
    public $id;
    public $activity;
}
