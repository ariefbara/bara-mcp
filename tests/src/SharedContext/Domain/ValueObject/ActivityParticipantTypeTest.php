<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class ActivityParticipantTypeTest extends TestBase
{
    protected $participantType;
    protected $activityParticipantType;


    protected function setUp(): void
    {
        parent::setUp();
        $this->participantType = ActivityParticipantType::COORDINATOR;
        $this->activityParticipantType = new TestableActivityParticipantType(ActivityParticipantType::COORDINATOR);
    }
    
    protected function executeConstruct()
    {
        return new TestableActivityParticipantType($this->participantType);
    }
    public function test_construct_setProperties()
    {
        $activityParticipantType = $this->executeConstruct();
        $this->assertEquals($this->participantType, $activityParticipantType->participantType);
    }
    public function test_construct_invalidType_badRequest()
    {
        $this->participantType = "invalid";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: invalid activity participant type";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_otherValidType()
    {
        $this->participantType = ActivityParticipantType::MANAGER;
        $this->executeConstruct();
        $this->participantType = ActivityParticipantType::CONSULTANT;
        $this->executeConstruct();
        $this->participantType = ActivityParticipantType::PARTICIPANT;
        $this->executeConstruct();
        $this->markAsSuccess();
    }
    
    public function test_sameValueAs_sameType_returnTrue()
    {
        $this->assertTrue($this->activityParticipantType->sameValueAs($this->activityParticipantType));
    }
    public function test_sameValueAs_differentType_returnFalse()
    {
        $other = new ActivityParticipantType(ActivityParticipantType::CONSULTANT);
        $this->assertFalse($this->activityParticipantType->sameValueAs($other));
    }
    
    public function test_isManagerType_aManagerType_returnTrue()
    {
        $this->activityParticipantType->participantType = ActivityParticipantType::MANAGER;
        $this->assertTrue($this->activityParticipantType->isManagerType());
    }
    public function test_isManagerType_notAManagerType_returnTrue()
    {
        $this->activityParticipantType->participantType = ActivityParticipantType::COORDINATOR;
        $this->assertFalse($this->activityParticipantType->isManagerType());
    }
    
    public function test_isCoordinatorType_aCoordinatorType_returnTrue()
    {
        $this->activityParticipantType->participantType = ActivityParticipantType::COORDINATOR;
        $this->assertTrue($this->activityParticipantType->isCoordinatorType());
    }
    public function test_isCoordinatorType_notACoordinatorType_returnTrue()
    {
        $this->activityParticipantType->participantType = ActivityParticipantType::MANAGER;
        $this->assertFalse($this->activityParticipantType->isCoordinatorType());
    }
    
    public function test_isConsultantType_aConsultantType_returnTrue()
    {
        $this->activityParticipantType->participantType = ActivityParticipantType::CONSULTANT;
        $this->assertTrue($this->activityParticipantType->isConsultantType());
    }
    public function test_isConsultantType_notAConsultantType_returnTrue()
    {
        $this->activityParticipantType->participantType = ActivityParticipantType::MANAGER;
        $this->assertFalse($this->activityParticipantType->isConsultantType());
    }
    
    public function test_isParticipantType_aParticipantType_returnTrue()
    {
        $this->activityParticipantType->participantType = ActivityParticipantType::PARTICIPANT;
        $this->assertTrue($this->activityParticipantType->isParticipantType());
    }
    public function test_isParticipantType_notAParticipantType_returnTrue()
    {
        $this->activityParticipantType->participantType = ActivityParticipantType::MANAGER;
        $this->assertFalse($this->activityParticipantType->isParticipantType());
    }
}

class TestableActivityParticipantType extends ActivityParticipantType
{
    public $participantType;
}
