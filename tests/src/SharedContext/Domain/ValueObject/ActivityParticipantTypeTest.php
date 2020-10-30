<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class ActivityParticipantTypeTest extends TestBase
{
    protected $participantTYpe = "coordinator";
    
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    protected function executeConstruct()
    {
        return new TestableActivityParticipantType($this->participantTYpe);
    }
    public function test_construct_setProperties()
    {
        $activityParticipantType = $this->executeConstruct();
        $this->assertEquals($this->participantTYpe, $activityParticipantType->participantType);
    }
    public function test_construct_invalidType_badRequest()
    {
        $this->participantTYpe = "invalid";
        $operation = function (){
            $this->executeConstruct();
        };
        $errorDetail = "bad request: invalid activity participant type";
        $this->assertRegularExceptionThrowed($operation, "Bad Request", $errorDetail);
    }
    public function test_construct_otherValidType()
    {
        $this->participantTYpe = "manager";
        $this->executeConstruct();
        $this->participantTYpe = "consultant";
        $this->executeConstruct();
        $this->participantTYpe = "participant";
        $this->executeConstruct();
        $this->markAsSuccess();
    }
}

class TestableActivityParticipantType extends ActivityParticipantType
{
    public $participantType;
}
