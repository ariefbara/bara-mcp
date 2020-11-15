<?php

namespace ActivityCreator\Domain\DependencyModel\Firm\Program;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Team\ProgramParticipation,
    Model\Activity,
    Model\Activity\Invitee,
    Model\ParticipantActivity,
    service\ActivityDataProvider
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ParticipantTest extends TestBase
{

    protected $program;
    protected $participant;
    protected $activityId = "activityId", $activityType, $activityDataProvider;
    protected $teamParticipant;
    protected $invitee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->participant = new TestableParticipant();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participant->program = $this->program;
        
        $this->teamParticipant = $this->buildMockOfClass(ProgramParticipation::class);
        $this->participant->teamParticipant = $this->teamParticipant;

        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
        
        $this->invitee = $this->buildMockOfClass(Invitee::class);
    }

    public function test_canInvolvedInProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->participant->canInvolvedInProgram($this->participant->program));
    }
    public function test_canInvolvedInProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->participant->canInvolvedInProgram($program));
    }
    
    protected function executeInitiateActivity()
    {
        $this->activityType->expects($this->any())
                ->method("canBeInitiatedBy")
                ->willReturn(true);
        
        return $this->participant->initiateActivity($this->activityId, $this->activityType, $this->activityDataProvider);
    }
    public function test_initiateActivity_returnParticipantActivity()
    {
        $activity = $this->buildMockOfClass(Activity::class);
        $this->program->expects($this->once())
                ->method("createActivity")
                ->with($this->activityId, $this->activityType, $this->activityDataProvider)
                ->willReturn($activity);
        
        $participantActivity = new ParticipantActivity($this->participant, $this->activityId, $activity);
        $this->assertEquals($participantActivity, $this->executeInitiateActivity());
    }
    public function test_initiateActivity_activityCannotBeInitiatedByParticipant_forbidden()
    {
        $this->activityType->expects($this->once())
                ->method("canBeInitiatedBy")
                ->with(new ActivityParticipantType(ActivityParticipantType::PARTICIPANT))
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateActivity();
        };
        $errorDetail = "forbidden: participant not allowed to initiate this activity";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_initiateActivity_inactiveParticipant_forbidden()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeInitiateActivity();
        };
        $errorDetail = "forbidden: only active participant can make this- request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_belongsToTeam_returnTeamParticipantsBelongsToTeamResult()
    {
        $this->teamParticipant->expects($this->once())
                ->method("belongsToTeam")
                ->with($teamId = "teamId");
        $this->participant->belongsToTeam($teamId);
    }
    public function test_belongsToTeam_notATeamParticipant_returnFalse()
    {
        $this->participant->teamParticipant = null;
        $this->assertFalse($this->participant->belongsToTeam("teamId"));
    }
    
    public function test_registerAsInviteeRecipient_registerAsInviteeRecipient()
    {
        $this->invitee->expects($this->once())
                ->method("registerParticipantAsRecipient")
                ->with($this->participant);
        $this->participant->registerAsInviteeRecipient($this->invitee);
    }

}

class TestableParticipant extends Participant
{

    public $program;
    public $id;
    public $active = true;
    
    public $teamParticipant;

    function __construct()
    {
        parent::__construct();
    }

}
