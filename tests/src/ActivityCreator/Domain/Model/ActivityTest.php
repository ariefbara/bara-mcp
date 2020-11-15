<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Program\ActivityType\ActivityParticipant,
    Model\Activity\ConsultantInvitee,
    Model\Activity\CoordinatorInvitee,
    Model\Activity\Invitee,
    Model\Activity\ManagerInvitee,
    Model\Activity\ParticipantInvitee,
    service\ActivityDataProvider
};
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\DateTimeInterval
};
use Tests\TestBase;

class ActivityTest extends TestBase
{

    protected $program;
    protected $activityType;
    protected $managerRepository, $coordinatorRepository, $consultantRepository, $participantRepository;
    protected $activity;
    protected $invitee;
    protected $id = "newId";
    protected $name = "new name";
    protected $description = "new description";
    protected $startTime;
    protected $endTime;
    protected $location = "new location";
    protected $note = "new note";
    protected $activityDataProvider;
    protected $activityParticipant;
    protected $canReceiveInvitation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->activityType = $this->buildMockOfClass(ActivityType::class);

        $activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
        $activityDataProvider->expects($this->any())->method("getName")->willReturn("name");
        $activityDataProvider->expects($this->any())->method("getStartTime")->willReturn(new DateTimeImmutable("+48 hours"));
        $activityDataProvider->expects($this->any())->method("getEndTime")->willReturn(new DateTimeImmutable("+50 hours"));
        $this->activity = new TestableActivity($this->program, "id", $this->activityType, $activityDataProvider);
        $this->invitee = $this->buildMockOfClass(Invitee::class);
        $this->activity->invitees->add($this->invitee);

        $this->startTime = new DateTimeImmutable("+72 hours");
        $this->endTime = new DateTimeImmutable("+75 hours");

        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
        
        $this->canReceiveInvitation = $this->buildMockOfInterface(CanReceiveInvitation::class);
        $this->activityParticipant = $this->buildMockOfClass(ActivityParticipant::class);
    }

    protected function getActivityDataProvider()
    {
        $this->activityDataProvider->expects($this->any())->method("getName")->willReturn($this->name);
        $this->activityDataProvider->expects($this->any())->method("getDescription")->willReturn($this->description);
        $this->activityDataProvider->expects($this->any())->method("getStartTime")->willReturn($this->startTime);
        $this->activityDataProvider->expects($this->any())->method("getEndTime")->willReturn($this->endTime);
        $this->activityDataProvider->expects($this->any())->method("getLocation")->willReturn($this->location);
        $this->activityDataProvider->expects($this->any())->method("getNote")->willReturn($this->note);
        return $this->activityDataProvider;
    }

    protected function executeConstruct()
    {
        return new TestableActivity($this->program, $this->id, $this->activityType, $this->getActivityDataProvider());
    }
    public function test_construct_setProperties()
    {
        $activity = $this->executeConstruct();
        $this->assertEquals($this->program, $activity->program);
        $this->assertEquals($this->id, $activity->id);
        $this->assertEquals($this->activityType, $activity->activityType);
        $this->assertEquals($this->name, $activity->name);
        $this->assertEquals($this->description, $activity->description);

        $startEndTime = new DateTimeInterval($this->startTime, $this->endTime);
        $this->assertEquals($startEndTime, $activity->startEndTime);

        $this->assertEquals($this->location, $activity->location);
        $this->assertEquals($this->note, $activity->note);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $activity->createdTime);
        $this->assertFalse($activity->cancelled);
        
        $this->assertInstanceOf(ArrayCollection::class, $activity->invitees);
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = "";
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: activity name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_nullStartTime_badRequest()
    {
        $this->startTime = null;
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: activity start time is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_nullEndTime_badRequest()
    {
        $this->endTime = null;
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = "bad request: activity end time is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_askActivityTypeToAddInvitees()
    {
        $this->activityType->expects($this->once())
                ->method("addInviteesToActivity")
                ->with($this->anything(), $this->activityDataProvider);
        $this->executeConstruct();
    }
    
    protected function executeUpdate()
    {
        $this->activity->update($this->getActivityDataProvider());
    }
    public function test_update_updateProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->activity->name);
        $this->assertEquals($this->description, $this->activity->description);

        $startEndTime = new DateTimeInterval($this->startTime, $this->endTime);
        $this->assertEquals($startEndTime, $this->activity->startEndTime);

        $this->assertEquals($this->location, $this->activity->location);
        $this->assertEquals($this->note, $this->activity->note);
    }
    public function test_update_cancelAllExistingInvitees()
    {
        $this->invitee->expects($this->once())
                ->method("cancelInvitation");
        $this->executeUpdate();
    }
    public function test_update_askActivityTypeToAddInvitees()
    {
        $this->activityType->expects($this->once())
                ->method("addInviteesToActivity")
                ->with($this->activity, $this->activityDataProvider);
        $this->executeUpdate();
    }
    
    protected function executeAddInvitee(): void
    {
        $this->canReceiveInvitation->expects($this->any())
                ->method("canInvolvedInProgram")
                ->willReturn(true);
        $this->activity->addInvitee($this->canReceiveInvitation, $this->activityParticipant);
    }
    public function test_addInvitee_addInviteeToList()
    {
        $this->executeAddInvitee();
        $this->assertEquals(2, $this->activity->invitees->count());
        $this->assertInstanceOf(Invitee::class, $this->activity->invitees->last());
    }
    public function test_addInvitee_RecipientCantBeInvolved_forbidden()
    {
        $this->canReceiveInvitation->expects($this->once())
                ->method("canInvolvedInProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeAddInvitee();
        };
        $errorDetail = "forbidden: invitee cannot be involved in program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_addInvitee_anInviteeCorrespondToRecipientExistInList_reinviteInviteeInList()
    {
        $this->invitee->expects($this->once())
                ->method("correspondWithRecipient")
                ->with($this->canReceiveInvitation)
                ->willReturn(true);
        $this->invitee->expects($this->once())
                ->method("reinvite");
        $this->executeAddInvitee();
    }
    public function test_addInvitee_anInviteeCorrespondToRecipientExist_preventAddNewInvitee()
    {
        $this->invitee->expects($this->once())
                ->method("correspondWithRecipient")
                ->willReturn(true);
        $this->executeAddInvitee();
        $this->assertEquals(1, $this->activity->invitees->count());
        
    }

}

class TestableActivity extends Activity
{

    public $program;
    public $id;
    public $activityType;
    public $name;
    public $description;
    public $startEndTime;
    public $location;
    public $note;
    public $cancelled;
    public $createdTime;
    public $invitees;

}
