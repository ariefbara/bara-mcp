<?php

namespace ActivityCreator\Domain\Model;

use ActivityCreator\Domain\ {
    DependencyModel\Firm\Manager,
    DependencyModel\Firm\Personnel\Consultant,
    DependencyModel\Firm\Personnel\Coordinator,
    DependencyModel\Firm\Program,
    DependencyModel\Firm\Program\ActivityType,
    DependencyModel\Firm\Program\Participant,
    Model\Activity\Invitation,
    service\ActivityDataProvider
};
use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\ValueObject\DateTimeInterval
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ActivityTest extends TestBase
{

    protected $program;
    protected $activityType;
    protected $managerRepository, $coordinatorRepository, $consultantRepository, $participantRepository;
    protected $activity;
    protected $id = "newId";
    protected $name = "new name";
    protected $description = "new description";
    protected $startTime;
    protected $endTime;
    protected $location = "new location";
    protected $note = "new note";
    protected $invitation;
    protected $activityDataProvider;
    protected $manager, $managerList = [];
    protected $coordinator, $coordinatorList = [];
    protected $consultant, $consultantList = [];
    protected $participant, $participantList = [];

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
        $this->invitation = $this->buildMockOfClass(Invitation::class);
        $this->activity->invitations->add($this->invitation);

        $this->startTime = new DateTimeImmutable("+72 hours");
        $this->endTime = new DateTimeImmutable("+75 hours");

        $this->activityDataProvider = $this->buildMockOfClass(ActivityDataProvider::class);
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
    }

    protected function getActivityDataProvider()
    {
        $this->activityDataProvider->expects($this->any())
                ->method("iterateInvitedManagerList")
                ->willReturn($this->managerList);
        $this->manager->expects($this->any())
                ->method("belongsToSameFirmAs")
                ->willReturn(true);
        
        $this->activityDataProvider->expects($this->any())
                ->method("iterateInvitedCoordinatorList")
                ->willReturn($this->coordinatorList);
        $this->coordinator->expects($this->any())
                ->method("belongsToProgram")
                ->willReturn(true);
        
        $this->activityDataProvider->expects($this->any())
                ->method("iterateInvitedConsultantList")
                ->willReturn($this->consultantList);
        $this->consultant->expects($this->any())
                ->method("belongsToProgram")
                ->willReturn(true);
        
        $this->activityDataProvider->expects($this->any())
                ->method("iterateInvitedParticipantList")
                ->willReturn($this->participantList);
        $this->participant->expects($this->any())
                ->method("belongsToProgram")
                ->willReturn(true);
        
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
        $this->activityType->expects($this->any())
                ->method("canInvite")
                ->willReturn(true);
        
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
    public function test_construct_containInvitedManagerList_inviteManagerInDataProviderInvitedList()
    {
        $this->managerList = [$this->manager];
        $activity = $this->executeConstruct();
        $this->assertEquals(1, $activity->invitations->count());
        $this->assertInstanceOf(Invitation::class, $activity->invitations->first());
    }
    public function test_construct_containInvitedManagerList_activityTypeDoesntAllowedManagerToBeInvited()
    {
        $this->managerList = [$this->manager];
        $this->activityType->expects($this->once())
                ->method("canInvite")
                ->with(new ActivityParticipantType(ActivityParticipantType::MANAGER))
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: cannot invite manager role";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_managerFromDifferentFirm_forbidden()
    {
        $this->managerList = [$this->manager];
        $this->manager->expects($this->once())
                ->method("belongsToSameFirmAs")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: unable to invite manager from different firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_containInvitedCoordinatorList_inviteCoordinatorInDataProviderInvitedList()
    {
        $this->coordinatorList = [$this->coordinator];
        $activity = $this->executeConstruct();
        $this->assertEquals(1, $activity->invitations->count());
        $this->assertInstanceOf(Invitation::class, $activity->invitations->first());
    }
    public function test_construct_containInvitedCoordinatorList_activityTypeDoesntAllowedCoordinatorToBeInvited()
    {
        $this->coordinatorList = [$this->coordinator];
        $this->activityType->expects($this->once())
                ->method("canInvite")
                ->with(new ActivityParticipantType(ActivityParticipantType::COORDINATOR))
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: cannot invite coordinator role";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_coordinatorFromDifferentFirm_forbidden()
    {
        $this->coordinatorList = [$this->coordinator];
        $this->coordinator->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: unable to invite coordinator from different program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_containInvitedConsultantList_inviteConsultantInDataProviderInvitedList()
    {
        $this->consultantList = [$this->consultant];
        $activity = $this->executeConstruct();
        $this->assertEquals(1, $activity->invitations->count());
        $this->assertInstanceOf(Invitation::class, $activity->invitations->first());
    }
    public function test_construct_containInvitedConsultantList_activityTypeDoesntAllowedConsultantToBeInvited()
    {
        $this->consultantList = [$this->consultant];
        $this->activityType->expects($this->once())
                ->method("canInvite")
                ->with(new ActivityParticipantType(ActivityParticipantType::CONSULTANT))
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: cannot invite consultant role";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_consultantFromDifferentFirm_forbidden()
    {
        $this->consultantList = [$this->consultant];
        $this->consultant->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: unable to invite consultant from different program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_containInvitedParticipantList_inviteParticipantInDataProviderInvitedList()
    {
        $this->participantList = [$this->participant];
        $activity = $this->executeConstruct();
        $this->assertEquals(1, $activity->invitations->count());
        $this->assertInstanceOf(Invitation::class, $activity->invitations->first());
    }
    public function test_construct_containInvitedParticipantList_activityTypeDoesntAllowedParticipantToBeInvited()
    {
        $this->participantList = [$this->participant];
        $this->activityType->expects($this->once())
                ->method("canInvite")
                ->with(new ActivityParticipantType(ActivityParticipantType::PARTICIPANT))
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: cannot invite participant role";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_construct_participantFromDifferentFirm_forbidden()
    {
        $this->participantList = [$this->participant];
        $this->participant->expects($this->once())
                ->method("belongsToProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "forbidden: unable to invite participant from different program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeUpdate()
    {
        $this->activityType->expects($this->any())
                ->method("canInvite")
                ->willReturn(true);
        
        $this->invitation->expects($this->any())
                ->method("isRemoved")
                ->willReturn(false);
        
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
    public function test_update_executeExistingInvitationsRemoveIfNotAppearInList()
    {
        $this->invitation->expects($this->once())
                ->method("removeIfNotAppearInList")
                ->with($this->activityDataProvider);
        $this->executeUpdate();
    }
    public function test_update_existingInvitationAlreadyRemoved_skipUpdating()
    {
        $this->invitation->expects($this->once())
                ->method("isRemoved")
                ->willReturn(true);
        $this->invitation->expects($this->never())
                ->method("removeIfNotAppearInList")
                ->with($this->activityDataProvider);
        $this->executeUpdate();
    }
    public function test_update_containInvitedManagerList_inviteManagerInDataProviderInvitedList()
    {
        $this->managerList = [$this->manager];
        $this->executeUpdate();
        $this->assertEquals(2, $this->activity->invitations->count());
        $this->assertInstanceOf(Invitation::class, $this->activity->invitations->last());
    }
    public function test_update_containInvitedManagerList_invitedManagerAlreadyExistInvitationList_prependAddNewInvitation()
    {
        $this->invitation->expects($this->once())
                ->method("isNonRemovedInvitationCorrespondWithManager")
                ->with($this->manager)
                ->willReturn(true);
        
        $this->managerList = [$this->manager];
        $this->executeUpdate();
        $this->assertEquals(1, $this->activity->invitations->count());
    }
    public function test_update_containInvitedCoordinatorList_inviteCoordinatorInDataProviderInvitedList()
    {
        $this->coordinatorList = [$this->coordinator];
        $this->executeUpdate();
        $this->assertEquals(2, $this->activity->invitations->count());
        $this->assertInstanceOf(Invitation::class, $this->activity->invitations->last());
    }
    public function test_update_containInvitedCoordinatorList_invitedCoordinatorAlreadyExistInvitationList_prependAddNewInvitation()
    {
        $this->invitation->expects($this->once())
                ->method("isNonRemovedInvitationCorrespondWithCoordinator")
                ->with($this->coordinator)
                ->willReturn(true);
        
        $this->coordinatorList = [$this->coordinator];
        $this->executeUpdate();
        $this->assertEquals(1, $this->activity->invitations->count());
    }
    public function test_update_containInvitedConsultantList_inviteConsultantInDataProviderInvitedList()
    {
        $this->consultantList = [$this->consultant];
        $this->executeUpdate();
        $this->assertEquals(2, $this->activity->invitations->count());
        $this->assertInstanceOf(Invitation::class, $this->activity->invitations->last());
    }
    public function test_update_containInvitedConsultantList_invitedConsultantAlreadyExistInvitationList_prependAddNewInvitation()
    {
        $this->invitation->expects($this->once())
                ->method("isNonRemovedInvitationCorrespondWithConsultant")
                ->with($this->consultant)
                ->willReturn(true);
        
        $this->consultantList = [$this->consultant];
        $this->executeUpdate();
        $this->assertEquals(1, $this->activity->invitations->count());
    }
    public function test_update_containInvitedParticipantList_inviteParticipantInDataProviderInvitedList()
    {
        $this->participantList = [$this->participant];
        $this->executeUpdate();
        $this->assertEquals(2, $this->activity->invitations->count());
        $this->assertInstanceOf(Invitation::class, $this->activity->invitations->last());
    }
    public function test_update_containInvitedParticipantList_invitedParticipantAlreadyExistInvitationList_prependAddNewInvitation()
    {
        $this->invitation->expects($this->once())
                ->method("isNonRemovedInvitationCorrespondWithParticipant")
                ->with($this->participant)
                ->willReturn(true);
        
        $this->participantList = [$this->participant];
        $this->executeUpdate();
        $this->assertEquals(1, $this->activity->invitations->count());
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
    public $invitations;

}
