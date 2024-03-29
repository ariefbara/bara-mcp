<?php

namespace Participant\Domain\DependencyModel\Firm\Program\Consultant;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\IContainParticipantReport;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\BookedMentoringSlot;
use Participant\Domain\Model\Participant\ContainSchedule;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\Schedule;
use Tests\TestBase;

class MentoringSlotTest extends TestBase
{

    protected $mentoringSlot, $schedule, $consultant, $consultationSetup;
    protected $bookedMentoringSlot;

    protected $program;
    protected $participant;
    protected $mentoring, $formRecordData, $mentorRating = 9;
    protected $otherMentoring;
    protected $otherSchedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoringSlot = new TestableMentoringSlot();
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->mentoringSlot->consultant = $this->consultant;
        $this->schedule = $this->buildMockOfClass(Schedule::class);
        $this->mentoringSlot->schedule = $this->schedule;
        
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->mentoringSlot->consultationSetup = $this->consultationSetup;

        $this->bookedMentoringSlot = $this->buildMockOfClass(BookedMentoringSlot::class);
        $this->mentoringSlot->bookedSlots = new ArrayCollection();
        $this->mentoringSlot->bookedSlots->add($this->bookedMentoringSlot);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        
        $this->mentoring = $this->buildMockOfInterface(IContainParticipantReport::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->otherMentoring = $this->buildMockOfInterface(ContainSchedule::class);
        $this->otherSchedule = $this->buildMockOfClass(Schedule::class);
    }
    
    protected function usableInProgram()
    {
        $this->consultant->expects($this->any())
                ->method('programEquals')
                ->with($this->program)
                ->willReturn(true);
        return $this->mentoringSlot->usableInProgram($this->program);
    }
    public function test_usableInProgram_returnConsultantProgramEqualsResult()
    {
        $this->consultant->expects($this->once())
                ->method('programEquals')
                ->with($this->program)
                ->willReturn(true);
        $this->assertTrue($this->usableInProgram());
    }
    public function test_usableInProgram_cancelled_returnFalse()
    {
        $this->mentoringSlot->cancelled = true;
        $this->assertFalse($this->usableInProgram());
    }
    
    protected function canAcceptBookingFrom()
    {
        $this->schedule->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->bookedMentoringSlot->expects($this->any())
                ->method('isActive')
                ->willReturn(true);
        return $this->mentoringSlot->canAcceptBookingFrom($this->participant);
    }
    public function test_canAcceptBookingFrom_returnTrue()
    {
        $this->assertTrue($this->canAcceptBookingFrom());
    }
    public function test_canAcceptBookingFrom_fullCapacity_returnFalse()
    {
        $this->mentoringSlot->capacity = 1;
        $this->assertFalse($this->canAcceptBookingFrom());
    }
    public function test_canAcceptBookingFrom_containCancelledBooking_excludeFromCapacityCheck()
    {
        $this->bookedMentoringSlot->expects($this->once())
                ->method('isActive')
                ->willReturn(false);
        $this->mentoringSlot->capacity = 1;
        $this->assertTrue($this->canAcceptBookingFrom());
    }
    public function test_canAcceptBookingFrom_containActiveBookingFromSameParticipant_returnFalse()
    {
        $this->bookedMentoringSlot->expects($this->once())
                ->method('isActiveBookingCorrespondWithParticipant')
                ->with($this->participant)
                ->willReturn(true);
        $this->mentoringSlot->capacity = 2;
        $this->assertFalse($this->canAcceptBookingFrom());
    }
    public function test_canAccepBookingFrom_notAnUpcomingSchedule_returnFalse()
    {
        $this->schedule->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertFalse($this->canAcceptBookingFrom());
    }
    
    protected function assertCancelBookingAllowed()
    {
        $this->schedule->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->mentoringSlot->assertCancelBookingAllowed();
    }
    public function test_assertCancelBookingAllowed_notAnUpcomingSchedule_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertCancelBookingAllowed();
        }, 'Forbidden', 'forbidden: can only cancel booking on upcoming schedule');
    }
    
    protected function processReportIn()
    {
        $this->schedule->expects($this->any())
                ->method('isAlreadyPassed')
                ->willReturn(true);
        $this->mentoringSlot->processReportIn($this->mentoring, $this->formRecordData, $this->mentorRating);
    }
    public function test_processReportIn_forwardToConsultationSetup()
    {
        $this->consultationSetup->expects($this->once())
                ->method('processReportIn')
                ->with($this->mentoring, $this->formRecordData, $this->mentorRating);
        $this->processReportIn();
    }
    public function test_processReportIn_notPastSchedule_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isAlreadyPassed')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->processReportIn();
        }, 'Forbidden', 'forbidden: can only submit report on past mentoring');
    }
    
    protected function scheduleInConflictWith()
    {
        return $this->mentoringSlot->scheduleInConflictWith($this->otherMentoring);
    }
    public function test_inConflictWith_returnOtherScheduleIntersectWithComparison()
    {
        $this->otherMentoring->expects($this->once())
                ->method('scheduleIntersectWith')
                ->with($this->schedule)
                ->willReturn(true);
        $this->assertTrue($this->scheduleInConflictWith());
    }
    
    protected function scheduleIntersectWith()
    {
        return $this->mentoringSlot->scheduleIntersectWith($this->otherSchedule);
    }
    public function test_schduleIntersectWith_returnScheduleIntersectWithComparison()
    {
        $this->schedule->expects($this->once())
                ->method('intersectWith')
                ->with($this->otherSchedule)
                ->willReturn(true);
        $this->assertTrue($this->scheduleIntersectWith());
    }
}

class TestableMentoringSlot extends MentoringSlot
{

    public $consultant;
    public $id;
    public $cancelled = false;
    public $schedule;
    public $capacity = 5;
    public $consultationSetup;
    public $bookedSlots;

    function __construct()
    {
        parent::__construct();
    }

}
