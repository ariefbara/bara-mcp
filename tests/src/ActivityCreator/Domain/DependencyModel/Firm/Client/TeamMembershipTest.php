<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Client;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Team\ProgramParticipation,
    Model\ParticipantActivity,
    service\ActivityDataProvider
};
use Tests\TestBase;

class TeamMembershipTest extends TestBase
{
    protected $teamMembership;
    protected $programParticipation, $activityId = "activityId", $activityType;
    protected $activityDataProvider;
    protected $participantActivity;

    protected function setUp(): void
    {
        parent::setUp();
        $this->teamMembership = new TestableTeamMembership();
        
        $this->programParticipation = $this->buildMockOfClass(ProgramParticipation::class);
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
        
        $this->participantActivity = $this->buildMockOfClass(ParticipantActivity::class);
    }
    
    protected function executeInitiateActivityInProgramParticipation()
    {
        $this->programParticipation->expects($this->any())
                ->method("belongsToTeam")
                ->willReturn(true);
        
        return $this->teamMembership->initiateActivityInProgramParticipation(
                $this->programParticipation, $this->activityId, $this->activityType, $this->activityDataProvider);
    }
    public function test_initiateActivityInProgramParticipation_returnProgramParticipationsInitiateActivityResult()
    {
        $this->programParticipation->expects($this->once())
                ->method("initiateActivity")
                ->with($this->activityId, $this->activityType, $this->activityDataProvider);
        $this->executeInitiateActivityInProgramParticipation();
    }
    public function test_initiateActivityInProgramParticipation_programParticipationDoesntBelongsToTeam_forbidden()
    {
        $this->programParticipation->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->teamMembership->teamId)
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateActivityInProgramParticipation();
        };
        $errorDetail = "forbidden: can't make request on program participation of other team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_initiateActivityInProgramParticipation_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $operation = function (){
            $this->executeInitiateActivityInProgramParticipation();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeUpdateActivity()
    {
        $this->participantActivity->expects($this->any())
                ->method("belongsToTeam")
                ->willReturn(true);
        
        $this->teamMembership->updateActivity($this->participantActivity, $this->activityDataProvider);
    }
    public function test_updateActivity_updateParticipantActivityActivity()
    {
        $this->participantActivity->expects($this->once())
                ->method("update")
                ->with($this->activityDataProvider);
        $this->executeUpdateActivity();
    }
    public function test_updateActivity_inactiveMember_forbidden()
    {
        $this->teamMembership->active = false;
        $operation = function (){
            $this->executeUpdateActivity();
        };
        $errorDetail = "forbidden: only active team member can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_updateActivity_participantActivityDoesntBelongsToTeam_forbidden()
    {
        $this->participantActivity->expects($this->once())
                ->method("belongsToTeam")
                ->with($this->teamMembership->teamId)
                ->willReturn(false);
        
        $operation = function (){
            $this->executeUpdateActivity();
        };
        $errorDetail = "forbidden: can't make request on activity not belongs to your team";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
}

class TestableTeamMembership extends TeamMembership
{
    public $client;
    public $id;
    public $teamId = "teamId";
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
