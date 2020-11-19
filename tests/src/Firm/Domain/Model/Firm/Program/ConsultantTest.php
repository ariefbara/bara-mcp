<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Personnel,
    Program,
    Program\MeetingType\Meeting\Attendee
};
use SharedContext\Domain\ValueObject\ActivityParticipantType;
use Tests\TestBase;

class ConsultantTest extends TestBase
{

    protected $program;
    protected $id = 'consultant-id';
    protected $personnel;
    protected $consultant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->consultant = new TestableConsultant($this->program, 'id', $this->personnel);
    }

    public function test_construct_setProperties()
    {
        $consultant = new TestableConsultant($this->program, $this->id, $this->personnel);
        $this->assertEquals($this->program, $consultant->program);
        $this->assertEquals($this->id, $consultant->id);
        $this->assertEquals($this->personnel, $consultant->personnel);
        $this->assertFalse($consultant->removed);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->consultant->remove();
        $this->assertTrue($this->consultant->removed);
    }

    public function test_reassign_setRemovedFlagFalse()
    {
        $this->consultant->removed = true;
        $this->consultant->reassign();
        $this->assertFalse($this->consultant->removed);
    }
    
    public function test_getPersonnelName_returnPersonnelsGetNameResult()
    {
        $this->personnel->expects($this->once())
                ->method('getName')
                ->willReturn($name = 'hadi pranoto');
        $this->assertEquals($name, $this->consultant->getPersonnelName());
    }
    
    public function test_canInvolvedInProgram_sameProgram_returnTrue()
    {
        $this->assertTrue($this->consultant->canInvolvedInProgram($this->consultant->program));
    }
    public function test_canInvolvedInProgram_differentProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->consultant->canInvolvedInProgram($program));
    }
    
    public function test_roleCorrespondWith_returnActivityParticipantTypeIsConsultantResult()
    {
        $activityParticipantType = $this->buildMockOfClass(ActivityParticipantType::class);
        $activityParticipantType->expects($this->once())
                ->method("isConsultantType");
        $this->consultant->roleCorrespondWith($activityParticipantType);
    }
    
    public function test_registerAsAttendeeCandidate_setConsultantAsAttendeeCandidate()
    {
        $attendee = $this->buildMockOfClass(Attendee::class);
        $attendee->expects($this->once())
                ->method("setConsultantAsAttendeeCandidate")
                ->with($this->consultant);
        $this->consultant->registerAsAttendeeCandidate($attendee);
    }

}

class TestableConsultant extends Consultant
{

    public $program, $id, $personnel, $removed;

}
