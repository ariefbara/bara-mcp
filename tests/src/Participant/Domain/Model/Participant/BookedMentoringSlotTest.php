<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\Model\Participant;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\Schedule;
use Tests\TestBase;

class BookedMentoringSlotTest extends TestBase
{
    protected $participant;
    protected $mentoringSlot;
    protected $bookedMentoringSlot, $mentoring;
    
    protected $id = 'newId';
    protected $mentorRating = 8, $formRecordData;
    protected $form;
    protected $otherMentoring;
    protected $otherSchedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->mentoringSlot = $this->buildMockOfClass(MentoringSlot::class);
        $this->bookedMentoringSlot = new TestableBookedMentoringSlot($this->participant, 'id', $this->mentoringSlot);
        
        $this->mentoring = $this->buildMockOfClass(Mentoring::class);
        $this->bookedMentoringSlot->mentoring = $this->mentoring;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->form = $this->buildMockOfClass(Form::class);
        $this->otherMentoring = $this->buildMockOfInterface(ContainSchedule::class);
        $this->otherSchedule = $this->buildMockOfClass(Schedule::class);
    }
    
    protected function construct()
    {
        return new TestableBookedMentoringSlot($this->participant, $this->id, $this->mentoringSlot);
    }
    public function test_construct_setProperties()
    {
        $bookedMentoringSlot = $this->construct();
        $this->assertSame($this->participant, $bookedMentoringSlot->participant);
        $this->assertSame($this->id, $bookedMentoringSlot->id);
        $this->assertSame($this->mentoringSlot, $bookedMentoringSlot->mentoringSlot);
        $this->assertFalse($bookedMentoringSlot->cancelled);
        $this->assertInstanceOf(Mentoring::class, $bookedMentoringSlot->mentoring);
    }
    public function test_construct_assertNoConflictWithParticipantOtherScheduleOrPotentialSchedule()
    {
        $this->participant->expects($this->once())
                ->method('assertNoConflictWithScheduledOrPotentialSchedule');
        $this->construct();
    }
    
    protected function isActive()
    {
        return $this->bookedMentoringSlot->isActive();
    }
    public function test_isActive_activeBooking_returnTrue()
    {
        $this->assertTrue($this->isActive());
    }
    public function test_isActive_cancelledBooking_returnFalse()
    {
        $this->bookedMentoringSlot->cancelled = true;
        $this->assertFalse($this->isActive());
    }
    
    protected function isActiveBookingCorrespondWithParticipant()
    {
        return $this->bookedMentoringSlot->isActiveBookingCorrespondWithParticipant($this->participant);
    }
    public function test_isActiveBookingCorrespondWithParticipant_activeBookingOfSameParticipant_returnTrue()
    {
        $this->assertTrue($this->isActiveBookingCorrespondWithParticipant());
    }
    public function test_isActiveBookingCorrespondWithParticipant_cancelledBooking_returnFalse()
    {
        $this->bookedMentoringSlot->cancelled = true;
        $this->assertFalse($this->isActiveBookingCorrespondWithParticipant());
    }
    public function test_isActiveBookingCorrespondWithParticipant_differentParticipant_returnFalse()
    {
        $this->bookedMentoringSlot->participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->isActiveBookingCorrespondWithParticipant());
    }
    
    protected function belongsToParticipant()
    {
        return $this->bookedMentoringSlot->belongsToParticipant($this->participant);
    }
    public function test_belongsToParticipant_sameParticipant_returnTrue()
    {
        $this->assertTrue($this->belongsToParticipant());
    }
    public function test_belongsToParticipant_differentParticipant_returnFalse()
    {
        $this->bookedMentoringSlot->participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->belongsToParticipant());
    }
    
    protected function assertManageableByParticipant()
    {
        $this->bookedMentoringSlot->assertManageableByParticipant($this->participant);
    }
    public function test_assertManageableByParticipant_void()
    {
        $this->assertManageableByParticipant();
        $this->markAsSuccess();
    }
    public function test_assertManageableByParticipant_cancelledSlot_forbidden()
    {
        $this->bookedMentoringSlot->cancelled = true;
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByParticipant();
        }, 'Forbidden', 'forbidden: booking already cancelled');
    }
    public function test_assertManageableByParticipant_differentParticipant_forbidden()
    {
        $this->bookedMentoringSlot->participant = $this->buildMockOfClass(Participant::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByParticipant();
        }, 'Forbidden', 'forbidden: can only managed owned booking slot');
    }
    
    protected function cancel()
    {
        $this->bookedMentoringSlot->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->cancel();
        $this->assertTrue($this->bookedMentoringSlot->cancelled);
    }
    public function test_cancel_assertMentoringSlotAllowBookingCancel()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('assertCancelBookingAllowed');
        $this->cancel();
    }
    
    protected function submitReport()
    {
        $this->bookedMentoringSlot->submitReport($this->mentorRating, $this->formRecordData);
    }
    public function test_submitReport_tellMentoringSlotToProcessReport()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('processReportIn')
                ->with($this->bookedMentoringSlot, $this->formRecordData, $this->mentorRating);
        $this->submitReport();
    }
    
    protected function processReport()
    {
        $this->bookedMentoringSlot->processReport($this->form, $this->formRecordData, $this->mentorRating);
    }
    public function test_processReport_submitParticipantReportInMentoring()
    {
        $this->mentoring->expects($this->once())
                ->method('submitParticipantReport')
                ->with($this->form, $this->formRecordData, $this->mentorRating);
        $this->processReport();
    }
    
    protected function inConflictWith()
    {
        $this->mentoringSlot->expects($this->any())
                ->method('scheduleInConflictWith')
                ->with($this->otherMentoring)
                ->willReturn(true);
        return $this->bookedMentoringSlot->aScheduledOrPotentialScheduleInConflictWith($this->otherMentoring);
    }
    public function test_inConflictWith_returnMentoringSlotScheduleInConflictWithComparison()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('scheduleInConflictWith')
                ->with($this->otherMentoring)
                ->willReturn(true);
        $this->assertTrue($this->inConflictWith());
    }
    public function test_inConflictWith_cancelledBooking_returnFalse()
    {
        $this->bookedMentoringSlot->cancelled = true;
        $this->assertFalse($this->inConflictWith());
    }
    public function test_inConflictWith_comparedWithSelf_returnFalse()
    {
        $this->otherMentoring = $this->bookedMentoringSlot;
        $this->assertFalse($this->inConflictWith());
    }
    
    protected function scheduleIntersectWith()
    {
        return $this->bookedMentoringSlot->scheduleIntersectWith($this->otherSchedule);
    }
    public function test_schduleIntersectWith_returnScheduleIntersectWithComparison()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('scheduleIntersectWith')
                ->with($this->otherSchedule)
                ->willReturn(true);
        $this->assertTrue($this->scheduleIntersectWith());
    }
}

class TestableBookedMentoringSlot extends BookedMentoringSlot
{
    public $participant;
    public $id;
    public $cancelled;
    public $mentoringSlot;
    public $mentoring;
}
