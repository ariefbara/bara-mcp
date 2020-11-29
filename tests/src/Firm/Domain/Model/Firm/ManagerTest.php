<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\ {
    Model\Firm,
    Model\Firm\Program\ActivityType,
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Coordinator,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Model\Firm\Program\MeetingType\MeetingData,
    Service\ActivityTypeDataProvider
};
use PHPUnit\Framework\MockObject\MockObject;
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ManagerTest extends TestBase
{

    protected $firm;
    protected $id = 'new-id', $name = 'new manager name', $email = 'new_address@email.org', $password = 'password123',
        $phone = '08112313123';
    protected $manager;
    protected $program;
    protected $activityTypeId = "activityTypeId", $activityTypeDataProvider;
    
    protected $meetingId = "meetingId", $meetingType, $meetingData;
    protected $coordinator;
    protected $consultant;
    protected $personnel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        
        $managerData = new ManagerData("name", "manager@email.org", "password123", "0823123123123");
        $this->manager = new TestableManager($this->firm, "id", $managerData);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
        
        $this->meetingType = $this->buildMockOfClass(ActivityType::class);
        $this->meetingData = $this->buildMockOfClass(MeetingData::class);
        
        $this->coordinator = $this->buildMockOfClass(Coordinator::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
    }
    
    protected function setAssetBelongsToFirm(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
    }
    protected function setAssetDoesntBelongsToFirm(MockObject $asset): void
    {
        $asset->expects($this->any())
                ->method("belongsToFirm")
                ->with($this->firm)
                ->willReturn(false);
    }
    protected function assertUnmanageableAssetForbiddenError(callable $operation): void
    {
        $errorDetail = "forbidden: unable to manage asset from other firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }

    protected function getManagerData()
    {
        return new ManagerData($this->name, $this->email, $this->password, $this->phone);
    }
    
    private function executeConstruct()
    {
        return new TestableManager($this->firm, $this->id, $this->getManagerData());
    }
    public function test_construct_setProperties()
    {
        $manager = $this->executeConstruct();
        $this->assertEquals($this->firm, $manager->firm);
        $this->assertEquals($this->id, $manager->id);
        $this->assertEquals($this->name, $manager->name);
        $this->assertEquals($this->email, $manager->email);
        $this->assertTrue($manager->password->match($this->password));
        $this->assertEquals($this->phone, $manager->phone);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $manager->joinTime->format('Y-m-d H:i:s'));
        $this->assertFalse($manager->removed);
    }
    public function test_construct_emptyName_throwEx()
    {
        $this->name = '';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager name is required';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_invalidEmail_throwEx()
    {
        $this->email = 'invalid address';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager email is required and must be in valid email format';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_invalidPhoneFormat_throwEx()
    {
        $this->phone = 'invalid phone format';
        $operation = function () {
            $this->executeConstruct();
        };
        $errorDetail = 'bad request: manager phone must be in valid phone format';
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_emptyPhone_processNormally()
    {
        $this->phone = '';
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    
    protected function executeCreateActivityTypeInProgram()
    {
        $this->program->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
        return $this->manager->createActivityTypeInProgram(
                $this->program, $this->activityTypeId, $this->activityTypeDataProvider);
    }
    public function test_createActivityTypeInProgram_returnActivityTypeCreatedInProgram()
    {
        $this->program->expects($this->once())
                ->method("createActivityType")
                ->with($this->activityTypeId, $this->activityTypeDataProvider);
        $this->executeCreateActivityTypeInProgram();
    }
    public function test_createActivityTypeInProgram_programFromDifferentFirm_forbidden()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm)
                ->willReturn(false);
        $operation = function (){
            $this->executeCreateActivityTypeInProgram();
        };
        $errorDetail = "forbidden: can only manage asset of same firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_createActivityTypeInProgram_inactiveManager_forbidden()
    {
        $this->manager->removed = true;
        $operation = function (){
            $this->executeCreateActivityTypeInProgram();
        };
        $errorDetail = "forbidden: only active manager can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_canInvolvedInProgram_returnProgramsBelongsToFirmResult()
    {
        $program = $this->buildMockOfClass(Program::class);
        $program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->manager->canInvolvedInProgram($program);
    }
    public function test_canInvolvedInProgram_inactiveManager_returnFalse()
    {
        $this->manager->removed = true;
        $program = $this->buildMockOfClass(Program::class);
        $program->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
        $this->assertFalse($this->manager->canInvolvedInProgram($program));
    }
    
    public function test_roleCorrespondWith_returnActivityParticipantTypeIsManagerResult()
    {
        $activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        $activityParticipantType->expects($this->once())
                ->method("isManagerType");
        $this->manager->roleCorrespondWith($activityParticipantType);
    }
    
    public function test_registerAsAttendeeCandidate_setManagerAsAttendeeCandidate()
    {
        $attendee = $this->buildMockOfClass(Attendee::class);
        $attendee->expects($this->once())
                ->method("setManagerAsAttendeeCandidate")
                ->with($this->manager);
        $this->manager->registerAsAttendeeCandidate($attendee);
    }
    
    protected function executeInitiateMeeting()
    {
        $this->meetingType->expects($this->any())
                ->method("belongsToFirm")
                ->willReturn(true);
        return $this->manager->initiateMeeting($this->meetingId, $this->meetingType, $this->meetingData);
    }
    public function test_initiateMeeting_returnMeetingCreatedThroughMeetingType()
    {
        $this->meetingType->expects($this->once())
                ->method("createMeeting")
                ->with($this->meetingId, $this->meetingData, $this->manager);
        $this->executeInitiateMeeting();
    }
    public function test_initiateMeeting_inactiveManager_forbidden()
    {
        $this->manager->removed = true;
        $operation = function (){
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: only active manager can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_initiateMeeting_meetingTypeBelongsToDifferentFirm_forbidden()
    {
        $this->meetingType->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm)
                ->willReturn(false);
        $operation = function (){
            $this->executeInitiateMeeting();
        };
        $errorDetail = "forbidden: unable to manage meeting type from other firm";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeDisableCoordinator()
    {
        $this->setAssetBelongsToFirm($this->coordinator);
        $this->manager->disableCoordinator($this->coordinator);
    }
    public function test_disableCoordinator_disableCoordinator()
    {
        $this->coordinator->expects($this->once())
                ->method("disable");
        $this->executeDisableCoordinator();
    }
    public function test_disableCoordinator_coordinatorBelongsToDifferentFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->coordinator);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeDisableCoordinator();
        });
    }
    
    protected function executeDisableConsultant()
    {
        $this->setAssetBelongsToFirm($this->consultant);
        $this->manager->disableConsultant($this->consultant);
    }
    public function test_disableConsultant_disableConsultant()
    {
        $this->consultant->expects($this->once())
                ->method("disable");
        $this->executeDisableConsultant();
    }
    public function test_disableConsultant_consultantFromOtherFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->consultant);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeDisableConsultant();
        });
    }
    
    protected function executeDisablePersonnel()
    {
        $this->setAssetBelongsToFirm($this->personnel);
        $this->manager->disablePersonnel($this->personnel);
    }
    public function test_disablePersonnel_disablePersonnel()
    {
        $this->personnel->expects($this->once())
                ->method("disable");
        $this->executeDisablePersonnel();
    }
    public function test_disablePersonnel_personnelFromOtherFirm_forbidden()
    {
        $this->setAssetDoesntBelongsToFirm($this->personnel);
        $this->assertUnmanageableAssetForbiddenError(function (){
            $this->executeDisablePersonnel();
        });
    }

}

class TestableManager extends Manager
{

    public $firm, $id, $name, $email, $password, $phone, $joinTime, $removed;
    public $adminAssignments;

}
