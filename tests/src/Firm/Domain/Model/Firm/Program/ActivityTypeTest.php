<?php

namespace Firm\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipant;
use Firm\Domain\Model\Firm\Program\ActivityType\ActivityParticipantData;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\ActivityType\MeetingData;
use Firm\Domain\Service\ActivityTypeDataProvider;
use Firm\Domain\Service\FeedbackFormRepository;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ActivityTypeTest extends TestBase
{
    protected $program;
    protected $activityType, $activityParticipant;
    protected $attendeeSetup;
    protected $id = "newId", $name = "new name", $description = "new description";
    protected $activityTypeDataProvider;
    protected $activityParticipantData;
    protected $meetingId = "meetingId", $meetingData, $user;
    protected $meeting;
    protected $firm;

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
        $this->meetingData->expects($this->any())->method("getStartTime")->willReturn(new DateTimeImmutable("+24 hours"));
        $this->meetingData->expects($this->any())->method("getEndTime")->willReturn(new DateTimeImmutable("+27 hours"));
        $this->user = $this->buildMockOfInterface(CanAttendMeeting::class);
        
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        
        $this->firm = $this->buildMockOfClass(Firm::class);
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
        $this->assertEquals(1, $this->activityType->participants->count());
        
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
    
    protected function executeAssertUsableInProgram()
    {
        $this->activityType->assertUsableInProgram($this->program);
    }
    public function test_assertUsableInProgram_activeActivityTypeInSameProgram()
    {
        $this->executeAssertUsableInProgram();
        $this->markAsSuccess();
    }
    public function test_assertUsableInProgram_inactiveActivityType_forbidden()
    {
        $this->activityType->disabled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertUsableInProgram();
        }, 'Forbidden', 'forbidden: unable to use activity type');
    }
    public function test_assertUsableInProgram_differentProgram_forbidden()
    {
        $this->activityType->program = $this->buildMockOfClass(Program::class);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertUsableInProgram();
        }, 'Forbidden', 'forbidden: unable to use activity type');
    }
    
    protected function executeAssertUsableInFirm()
    {
        $this->program->expects($this->any())
                ->method('belongsToFirm')
                ->with($this->firm)
                ->willReturn(true);
        $this->activityType->assertUsableInFirm($this->firm);
    }
    public function test_assertUsableInFirm_activeActivityTypeInSameFirm()
    {
        $this->executeAssertUsableInFirm();
        $this->markAsSuccess();
    }
    public function test_assertUsableInFirm_inactiveActivityType_forbidden()
    {
        $this->activityType->disabled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertUsableInFirm();
        }, 'Forbidden', 'forbidden: unable to use activity type');
    }
    public function test_assertUsableInFirm_programDoesntBelongsToFirm_forbidden()
    {
        $this->program->expects($this->once())
                ->method('belongsToFirm')
                ->with($this->firm)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeAssertUsableInFirm();
        }, 'Forbidden', 'forbidden: unable to use activity type');
    }
    
    protected function executeGetActiveAttendeeSetupCorrenspondWithRoleOrDie()
    {
        return $this->activityType->getActiveAttendeeSetupCorrenspondWithRoleOrDie($this->activityParticipantType);
    }
    public function test_getActiveAttendeeSetupCorrespondWithRoleOrDie_hasActiveActivityParticipantCorrespondWithSameRole_returnActivityParticipant()
    {
        $this->attendeeSetup->expects($this->once())
                ->method('isActiveTypeCorrespondWithRole')
                ->with($this->activityParticipantType)
                ->willReturn(true);
        $this->assertEquals($this->attendeeSetup, $this->executeGetActiveAttendeeSetupCorrenspondWithRoleOrDie());
    }
    public function test_getActiveAttendeeSetupCorrespondWithRole_noAttendeeSetupSatisfiedCritera_notFound()
    {
        $this->assertRegularExceptionThrowed(function (){
            $this->executeGetActiveAttendeeSetupCorrenspondWithRoleOrDie();
        }, 'Not Found', 'not found: no attendee setup correspond with user role');
    }
    
    protected function executeCreateMeeting()
    {
        return $this->activityType->createMeeting($this->meetingId, $this->meetingData);
    }
    public function test_createMeeting_returnMeeting()
    {
        $this->assertInstanceOf(Meeting::class, $this->executeCreateMeeting());
    }
    public function test_createMeeting_disabledActivityType_forbidden()
    {
        $this->activityType->disabled = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeCreateMeeting();
        }, 'Forbidden', 'forbidden: inactive activity type');
    }
    
    public function test_inviteAllActiveProgramParticipantsToMeeting_executeProgramsInviteAllActiveParticipantsToMeeting()
    {
        $this->program->expects($this->once())
                ->method('inviteAllActiveParticipantsToMeeting')
                ->with($this->meeting);
        $this->activityType->inviteAllActiveProgramParticipantsToMeeting($this->meeting);
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
