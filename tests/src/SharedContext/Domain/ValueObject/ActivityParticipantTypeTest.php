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
}

class TestableActivityParticipantType extends ActivityParticipantType
{
    public $participantType;
}
