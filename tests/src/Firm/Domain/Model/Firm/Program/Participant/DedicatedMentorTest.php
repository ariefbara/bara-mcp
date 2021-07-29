<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use DateTimeImmutable;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Consultant;
use Firm\Domain\Model\Firm\Program\ActivityType\Meeting;
use Firm\Domain\Model\Firm\Program\Participant;
use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class DedicatedMentorTest extends TestBase
{
    protected $participant;
    protected $consultant;
    protected $dedicatedMentor;
    protected $id = 'newId';
    
    protected $meeting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->consultant->expects($this->any())->method('isActive')->willReturn(true);
        $this->dedicatedMentor = new TestableDedicatedMentor($this->participant, 'id', $this->consultant);
        $this->dedicatedMentor->modifiedTime = new DateTimeImmutable('-2 days');
        
        $this->meeting = $this->buildMockOfClass(Meeting::class);
    }
    
    protected function executeConstruct()
    {
        return new TestableDedicatedMentor($this->participant, $this->id, $this->consultant);
    }
    public function test_construct_setProperties()
    {
        $dedicatedMentor = $this->executeConstruct();
        $this->assertEquals($this->participant, $dedicatedMentor->participant);
        $this->assertEquals($this->id, $dedicatedMentor->id);
        $this->assertEquals($this->consultant, $dedicatedMentor->consultant);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $dedicatedMentor->modifiedTime);
        $this->assertFalse($dedicatedMentor->cancelled);
    }
    public function test_construct_inactiveConsultant_forbidden()
    {
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $operation = function (){
            $this->executeConstruct();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: unable to dedicate inactive consultant');
    }
    
    public function test_belongsToProgram_returnParticipantsBelongsToProgramResult()
    {
        $this->participant->expects($this->once())
                ->method('belongsToProgram');
        $this->dedicatedMentor->belongsToProgram($this->buildMockOfClass(Program::class));
    }
    
    public function test_consultantEquals_sameConsultant_returnTrue()
    {
        $this->assertTrue($this->dedicatedMentor->consultantEquals($this->consultant));
    }
    public function test_consultantEquals_differentConsultant_returnFalse()
    {
        $consultant = $this->buildMockOfClass(Consultant::class);
        $this->assertFalse($this->dedicatedMentor->consultantEquals($consultant));
    }
    
    protected function executeCancel()
    {
        $this->dedicatedMentor->cancel();
    }
    public function test_cancel_setCancelledAndModifiedTime()
    {
        $this->executeCancel();
        $this->assertTrue($this->dedicatedMentor->cancelled);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->dedicatedMentor->modifiedTime);
    }
    public function test_cancel_alreadyCancelled_forbidden()
    {
        $this->dedicatedMentor->cancelled = true;
        $operation = function (){
            $this->executeCancel();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: dedicated mentor already cancelled');
    }
    
    protected function executeReassign()
    {
        $this->dedicatedMentor->reassign();
    }
    public function test_reassign_setCancelledStatusAndModifiedTime()
    {
        $this->dedicatedMentor->cancelled = true;
        $this->executeReassign();
        $this->assertFalse($this->dedicatedMentor->cancelled);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $this->dedicatedMentor->modifiedTime);
    }
    public function test_reassign_inactiveConsultant_forbidden()
    {
        $this->dedicatedMentor->cancelled = true;
        $this->dedicatedMentor->consultant = $this->buildMockOfClass(Consultant::class);
        $operation = function (){
            $this->executeReassign();
        };
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', 'forbidden: unable to dedicate inactive consultant');
    }
    public function test_reassign_stillActiveDedication_NOP()
    {
        $this->dedicatedMentor->cancelled = false;
        $modifiedTime = $this->dedicatedMentor->modifiedTime;
        $this->executeReassign();
        $this->assertEquals($modifiedTime, $this->dedicatedMentor->modifiedTime);
    }
    
    public function test_isActiveAssignment_active_returnTrue()
    {
        $this->assertTrue($this->dedicatedMentor->isActiveAssignment());
    }
    public function test_isActiveAssignment_cancelled_returnFalse()
    {
        $this->dedicatedMentor->cancelled = true;
        $this->assertFalse($this->dedicatedMentor->isActiveAssignment());
    }
    
    public function test_inviteParticipantToMeeting_inviteParticipantToMeeting()
    {
        $this->participant->expects($this->once())
                ->method('inviteToMeeting')
                ->with($this->meeting);
        $this->dedicatedMentor->inviteParticipantToMeeting($this->meeting);
    }
}

class TestableDedicatedMentor extends DedicatedMentor
{
    public $participant;
    public $id;
    public $consultant;
    public $modifiedTime;
    public $cancelled;
}
