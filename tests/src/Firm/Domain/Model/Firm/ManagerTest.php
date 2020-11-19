<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\ {
    Model\Firm,
    Model\Firm\Program\MeetingType\Meeting\Attendee,
    Service\ActivityTypeDataProvider
};
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        
        $managerData = new ManagerData("name", "manager@email.org", "password123", "0823123123123");
        $this->manager = new TestableManager($this->firm, "id", $managerData);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->activityTypeDataProvider = $this->buildMockOfClass(ActivityTypeDataProvider::class);
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

}

class TestableManager extends Manager
{

    public $firm, $id, $name, $email, $password, $phone, $joinTime, $removed;
    public $adminAssignments;

}
