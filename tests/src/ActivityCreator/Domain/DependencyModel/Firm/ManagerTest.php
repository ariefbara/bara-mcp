<?php

namespace ActivityCreator\Domain\DependencyModel\Firm;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program\ActivityType,
    Model\Activity,
    Model\Activity\Invitee,
    Model\ManagerActivity
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ManagerTest extends TestBase
{
    protected $manager;
    protected $program;
    protected $activityId = "activityId", $activityType, $activityDataProvider = "string represent data provider";
    protected $invitee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TestableManager();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->activityType = $this->buildMockOfClass(ActivityType::class);
        
        $this->invitee = $this->buildMockOfClass(Invitee::class);
    }
    public function test_canInvolvedInProgram_returnProgramFirmIdEqualsResult()
    {
        $this->program->expects($this->once())
                ->method("firmIdEquals")
                ->with($this->manager->firmId);
        $this->manager->canInvolvedInProgram($this->program);
    }
    
    protected function executeInitiateActivityInProgram()
    {
        $this->program->expects($this->any())
                ->method("firmIdEquals")
                ->willReturn(true);
        
        $this->activityType->expects($this->any())
                ->method("canBeInitiatedBy")
                ->willReturn(true);
        
        return $this->manager->initiateActivityInProgram(
                $this->activityId, $this->program, $this->activityType, $this->activityDataProvider);
    }
    public function test_inititaeActivityInProgram_returnManagerActivity()
    {
        $activity = $this->buildMockOfClass(Activity::class);
        $this->program->expects($this->once())
                ->method("createActivity")
                ->with($this->activityId, $this->activityType, $this->activityDataProvider)
                ->willReturn($activity);
        $managerActivity = new ManagerActivity($this->manager, $this->activityId, $activity);
        $this->assertEquals($managerActivity, $this->executeInitiateActivityInProgram());
    }
    public function test_initiateActivityInProgram_programFromDifferentFirm_forbidden()
    {
        $this->program->expects($this->once())
                ->method("firmIdEquals")
                ->with($this->manager->firmId)
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateActivityInProgram();
        };
        $errorDetail = "forbidden: unable to create activity in program of other firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_initiateActivityInProgram_activityTypeCantBeInitatedByManager_forbidden()
    {
        $this->activityType->expects($this->once())
                ->method("canBeInitiatedBy")
                ->with(new ActivityParticipantType(ActivityParticipantType::MANAGER))
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateActivityInProgram();
        };
        $errorDetail = "forbidden: this activity can't be initiated by manager role";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_registerAsInviteeRecipient_registerAsInviteeRecipient()
    {
        $this->invitee->expects($this->once())
                ->method("registerManagerAsRecipient")
                ->with($this->manager);
        $this->manager->registerAsInviteeRecipient($this->invitee);
    }
}

class TestableManager extends Manager
{
    public $firmId = "firmId";
    public $id;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
