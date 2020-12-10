<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\ {
    Model\Firm,
    Model\Firm\Program,
    Model\Firm\Program\ActivityType\ActivityParticipant,
    Model\Firm\Program\ActivityType\ActivityParticipantData,
    Model\Firm\Program\MeetingType\CanAttendMeeting,
    Model\Firm\Program\MeetingType\Meeting,
    Model\Firm\Program\MeetingType\MeetingData,
    Service\ActivityTypeDataProvider,
    Service\FeedbackFormRepository
};
use Tests\TestBase;

class ActivityTypeTest extends TestBase
{
    protected $program;
    protected $activityType;
    protected $attendeeSetup;
    protected $id = "newId", $name = "new name", $description = "new description";
    protected $activityTypeDataProvider;
    protected $activityParticipantData;
    protected $meetingId = "meetingId", $meetingData, $user;
    protected $meeting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        
        $feedbackFormRepository = $this->buildMockOfInterface(FeedbackFormRepository::class);
        $activityTypeDataProvider = new ActivityTypeDataProvider($feedbackFormRepository, "name", "description");
        $this->activityType = new TestableActivityType($this->program, "id", $activityTypeDataProvider);
        
        $this->attendeeSetup = $this->buildMockOfClass(ActivityParticipant::class);
        $this->activityType->participants->add($this->attendeeSetup);
        
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
        $this->activityParticipantData = $this->buildMockOfClass(ActivityParticipantData::class);
        $this->activityParticipantData->expects($this->any())->method("getParticipantType")->willReturn("consultant");
        
        
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        $this->meetingData->expects($this->any())->method("getName")->willReturn("new name");
        $this->meetingData->expects($this->any())->method("getStartTime")->willReturn(new \DateTimeImmutable("+24 hours"));
        $this->meetingData->expects($this->any())->method("getEndTime")->willReturn(new \DateTimeImmutable("+27 hours"));
        $this->user = $this->buildMockOfInterface(CanAttendMeeting::class);
        
        $this->meeting = $this->buildMockOfClass(Meeting::class);
    }
    
    protected function executeConstruct()
    {
        $this->activityTypeDataProvider->expects($this->any())->method("getName")->willReturn($this->name);
        $this->activityTypeDataProvider->expects($this->any())->method("getDescription")->willReturn($this->description);
        return new TestableActivityType($this->program, $this->id, $this->activityTypeDataProvider);
    }
    public function test_construct_setProperties()
    {
        $activityType = $this->executeConstruct();
        $this->assertEquals($this->program, $activityType->program);
        $this->assertEquals($this->id, $activityType->id);
        $this->assertEquals($this->name, $activityType->name);
        $this->assertEquals($this->description, $activityType->description);
        $this->assertFalse($activityType->disabled);
    }
    public function test_construct_emptyName_badRequest()
    {
        $this->name = "";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: activity type name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_addActivityParticipant()
    {
        $this->activityParticipantData->expects($this->any())->method("getParticipantType")->willReturn("coordinator");
        $this->activityParticipantData->expects($this->any())->method("getCanInitiate")->willReturn(false);
        $this->activityParticipantData->expects($this->any())->method("getCanAttend")->willReturn(true);
        $this->activityTypeDataProvider->expects($this->once())
                ->method("iterateActivityParticipantData")
                ->willReturn([$this->activityParticipantData]);
        $activityType = $this->executeConstruct();
        $this->assertEquals(1, $activityType->participants->count());
        $this->assertInstanceOf(ActivityParticipant::class, $activityType->participants->first());
    }
    
    protected function executeUpdate()
    {
        $this->activityTypeDataProvider->expects($this->any())->method("getName")->willReturn($this->name);
        $this->activityTypeDataProvider->expects($this->any())->method("getDescription")->willReturn($this->description);
        $this->activityType->update($this->activityTypeDataProvider);
    }
    public function test_update_changeProperties()
    {
        $this->executeUpdate();
        $this->assertEquals($this->name, $this->activityType->name);
        $this->assertEquals($this->description, $this->activityType->description);
    }
    public function test_update_emptyName_badRequst()
    {
        $this->name = "";
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "bad request: activity type name is mandatory";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_update_updateAllExistingAttendeeSetup()
    {
        $this->attendeeSetup->expects($this->once())
                ->method("update")
                ->with($this->activityTypeDataProvider);
        $this->executeUpdate();
    }
    public function test_update_addLeftOverAttendeeSetupToCollection()
    {
        $this->activityParticipantData->expects($this->any())->method("getParticipantType")->willReturn("coordinator");
        $this->activityParticipantData->expects($this->any())->method("getCanInitiate")->willReturn(false);
        $this->activityParticipantData->expects($this->any())->method("getCanAttend")->willReturn(true);
        $this->activityTypeDataProvider->expects($this->once())
                ->method("iterateActivityParticipantData")
                ->willReturn([$this->activityParticipantData]);
        
        $this->executeUpdate();
        $this->assertEquals(2, $this->activityType->participants->count());
        $this->assertInstanceOf(ActivityParticipant::class, $this->activityType->participants->last());
    }
    
    public function test_disable_setDisabled()
    {
        $this->activityType->disable();
        $this->assertTrue($this->activityType->disabled);
    }
    
    public function test_enable_setDisabledFalse()
    {
        $this->activityType->disabled = true;
        $this->activityType->enable();
        $this->assertFalse($this->activityType->disabled);
    }
    
    public function test_belongsToFirm_returnProgramBelongsToFirmResult()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($firm);
        $this->activityType->belongsToFirm($firm);
    }
    
    public function test_belongsToProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->activityType->belongsToProgram($this->activityType->program));
    }
    public function test_belongsToProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->activityType->belongsToProgram($program));
    }
    
    protected function executeCreateMeeting()
    {
        $this->attendeeSetup->expects($this->any())
                ->method("roleCorrespondWithUser")
                ->willReturn(true);
        return $this->activityType->createMeeting($this->meetingId, $this->meetingData, $this->user);
    }
    public function test_createMeeting_returnMeeting()
    {
        $this->assertInstanceOf(Meeting::class, $this->executeCreateMeeting());
    }
    public function test_createMeeting_disabledActivityType_forbidden()
    {
        $this->activityType->disabled = true;
        $operation = function (){
            $this->executeCreateMeeting();
        };
        $errorDetail = "Forbidden: can only create meeting on enabled type";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeSetUserAsInitiatorInMeeting()
    {
        $this->attendeeSetup->expects($this->any())
                ->method("roleCorrespondWithUser")
                ->willReturn(true);
        $this->activityType->setUserAsInitiatorInMeeting($this->meeting, $this->user);
    }
    public function test_setUserAsInitiatorInMeeting_setMeetingInitiatorThroudCorrespondingAttendeeSetup()
    {
        $this->attendeeSetup->expects($this->once())
                ->method("setUserAsInitiatorInMeeting")
                ->with($this->meeting, $this->user);
        $this->executeSetUserAsInitiatorInMeeting();
    }
    public function test_setUserAsInitiatorInMeeting_noAttendeeSetupRoleCorrespondWithUser_forbidden()
    {
        $this->attendeeSetup->expects($this->once())
                ->method("roleCorrespondWithUser")
                ->with($this->user)
                ->willReturn(false);
        $operation = function (){
            $this->executeSetUserAsInitiatorInMeeting();
        };
        $errorDetail = "forbidden: this user type is not allowed to involved in meeting type";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeAddUserAsAttendeeInMeeting()
    {
        $this->user->expects($this->any())
                ->method("canInvolvedInProgram")
                ->willReturn(true);
        $this->attendeeSetup->expects($this->any())
                ->method("roleCorrespondWithUser")
                ->willReturn(true);
        $this->activityType->addUserAsAttendeeInMeeting($this->meeting, $this->user);
    }
    public function test_addUSerAsAttendeeInMeeting_addMeetingAttendeeThroughAttendeeSetupCorrespondWithUser()
    {
        $this->attendeeSetup->expects($this->once())
                ->method("addUserAsAttendeeInMeeting")
                ->with($this->meeting, $this->user);
        $this->executeAddUserAsAttendeeInMeeting();
    }
    public function test_addUserAsAttendeeInMeeting_userCannotInvolvedInSameProgram()
    {
        $this->user->expects($this->once())
                ->method("canInvolvedInProgram")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeAddUserAsAttendeeInMeeting();
        };
        $errorDetail = "forbidden: user cannot be involved in program";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
}

class TestableActivityType extends ActivityType
{
    public $program;
    public $id;
    public $name;
    public $description;
    public $disabled;
    public $participants;
}
