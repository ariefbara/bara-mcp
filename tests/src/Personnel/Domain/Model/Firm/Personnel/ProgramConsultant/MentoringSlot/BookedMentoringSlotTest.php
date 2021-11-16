<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class BookedMentoringSlotTest extends TestBase
{
    protected $bookedSlot, $mentoring;
    protected $mentoringSlot;
    protected $mentor;
    
    protected $formRecordData, $participantRating = 9;
    protected $form;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookedSlot = new TestableBookedMentoringSlot();
        $this->mentoring = $this->buildMockOfClass(\SharedContext\Domain\Model\Mentoring::class);
        $this->bookedSlot->mentoring = $this->mentoring;
        $this->mentoringSlot = $this->buildMockOfClass(MentoringSlot::class);
        $this->bookedSlot->mentoringSlot = $this->mentoringSlot;
        
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->form = $this->buildMockOfClass(Form::class);
    }
    
    protected function assertManageableByMentor()
    {
        $this->mentoringSlot->expects($this->any())
                ->method('belongsToMentor')
                ->with($this->mentor)
                ->willReturn(true);
        $this->bookedSlot->assertManageableByMentor($this->mentor);
    }
    public function test_assertManageableByMentor_cancelledBooking_forbidden()
    {
        $this->bookedSlot->cancelled = true;
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByMentor();
        }, 'Forbidden', 'forbidden: can only manage active booking');
    }
    public function test_assertManageableByMentor_mentoringSlotDoesntBelongsToMentor_forbidden()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('belongsToMentor')
                ->with($this->mentor)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByMentor();
        }, 'Forbidden', 'forbidden: can only manage booking on owned slot');
    }
    
    protected function belongsToMentor()
    {
        return $this->bookedSlot->belongsToMentor($this->mentor);
    }
    public function test_belongsToMentor_returnMentoringSlotsBelongsToMentorResult()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('belongsToMentor')
                ->with($this->mentor);
        $this->belongsToMentor();
    }
    
    protected function cancel()
    {
        $this->mentoringSlot->expects($this->any())
                ->method('isUpcomingSchedule')
                ->willReturn(true);
        $this->bookedSlot->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->cancel();
        $this->assertTrue($this->bookedSlot->cancelled);
    }
    public function test_cancel_mentoringSlotIsNotAnUpcomingSchedule_forbidden()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('isUpcomingSchedule')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->cancel();
        }, 'Forbidden', 'forbidden: unable to cancel an already passed booking');
    }
    
    protected function isActiveBooking()
    {
        return $this->bookedSlot->isActiveBooking();
    }
    public function test_isActiveBooking_activeBooking_returnTrue()
    {
        $this->assertTrue($this->isActiveBooking());
    }
    public function test_isActiveBooking_cancelledBooking_returnFalse()
    {
        $this->bookedSlot->cancelled = true;
        $this->assertFalse($this->isActiveBooking());
    }
    
    protected function submitReport()
    {
        $this->bookedSlot->submitReport($this->formRecordData, $this->participantRating);
    }
    public function test_submitReport_askMentoringSlotToProcessReport()
    {
        $this->mentoringSlot->expects($this->once())
                ->method('processReportIn')
                ->with($this->bookedSlot, $this->formRecordData, $this->participantRating);
        $this->submitReport();
    }
    public function test_submitReport_cancelledBooking_forbidden()
    {
        $this->bookedSlot->cancelled = true;
        $this->assertRegularExceptionThrowed(function() {
            $this->submitReport();
        }, 'Forbidden', 'forbidden: unable to submit report on cancelled booking');
    }
    
    protected function processReport()
    {
        $this->bookedSlot->processReport($this->form, $this->formRecordData, $this->participantRating);
    }
    public function test_processReport_submitMentorReportInMentoring()
    {
        $this->mentoring->expects($this->once())
                ->method('submitMentorReport')
                ->with($this->form, $this->formRecordData, $this->participantRating);
        $this->processReport();
    }
}

class TestableBookedMentoringSlot extends BookedMentoringSlot
{
    public $mentoringSlot;
    public $id = 'id';
    public $cancelled = false;
    public $mentoring;
    
    function __construct()
    {
        parent::__construct();
    }
}
