<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Personnel\Domain\Model\Firm\ContainMentorReport;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringSlot\BookedMentoringSlot;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;
use Tests\TestBase;

class MentoringSlotTest extends TestBase
{
    protected $mentor;
    protected $consultationSetup;
    protected $mentoringSlot, $schedule;
    protected $bookedSlot;

    protected $id = 'newId', $startTime, $endTime, $mediaType = 'media type', $location = 'location', $capacity = 5;
    
    protected $containMentorReport, $formRecordData, $participantRating = 9;
    protected $containSchedule;
    protected $otherSchedule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $scheduleData = new ScheduleData(
                new DateTimeImmutable('+24 hours'), new DateTimeImmutable('+25 hours'), 'online', 'meet.google.com');
        $mentoringSlotData = new MentoringSlotData($scheduleData, 4);
        
        $this->mentoringSlot = new TestableMentoringSlot($this->mentor, 'id', $this->consultationSetup, $mentoringSlotData);
        $this->schedule = $this->buildMockOfClass(Schedule::class);
        $this->mentoringSlot->schedule = $this->schedule;
        
        $this->bookedSlot = $this->buildMockOfClass(BookedMentoringSlot::class);
        $this->mentoringSlot->bookedSlots = new ArrayCollection();
        $this->mentoringSlot->bookedSlots->add($this->bookedSlot);
        
        $this->startTime = new DateTimeImmutable('+48 hours');
        $this->endTime = new DateTimeImmutable('+50 hours');
        
        $this->containMentorReport = $this->buildMockOfInterface(ContainMentorReport::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->containSchedule = $this->buildMockOfInterface(ContainSchedule::class);
        $this->otherSchedule = $this->buildMockOfClass(Schedule::class);
    }
    
    protected function getScheduleData()
    {
        return new ScheduleData($this->startTime, $this->endTime, $this->mediaType, $this->location);
    }
    protected function getMentoringSlotData()
    {
        return new MentoringSlotData($this->getScheduleData(), $this->capacity);
    }
    
    protected function construct()
    {
        return new TestableMentoringSlot($this->mentor, $this->id, $this->consultationSetup, $this->getMentoringSlotData());
    }
    public function test_construct_setProperties()
    {
        $mentoringSlot = $this->construct();
        $this->assertSame($this->mentor, $mentoringSlot->mentor);
        $this->assertSame($this->id, $mentoringSlot->id);
        $this->assertFalse($mentoringSlot->cancelled);
        $schedule = new Schedule($this->getScheduleData());
        $this->assertEquals($schedule, $mentoringSlot->schedule);
        $this->assertSame($this->capacity, $mentoringSlot->capacity);
        $this->assertSame($this->consultationSetup, $mentoringSlot->consultationSetup);
    }
    public function test_construct_notUpcomingSchedule_badRequest()
    {
        $this->startTime = new \DateTimeImmutable('-1 hours');
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: mentoring slot must be an upcoming schedule');
    }
    public function test_construct_emptyCapacity_badRequest()
    {
        $this->capacity = 0;
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: mentoring slot capacity is mandatory');
    }
    public function test_construct_assertScheduleNotInConflictWithMentorsOtherScheduleOrPotentialSchedule()
    {
        $this->mentor->expects($this->once())
                ->method('assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule');
        $this->construct();
    }
    
    protected function isUpcomingSchedule()
    {
        return $this->mentoringSlot->isUpcomingSchedule();
    }
    public function test_isUpcomingSchedule_returnScheduleIsUpcomingResult()
    {
        $this->schedule->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->assertTrue($this->isUpcomingSchedule());
    }
    
    protected function belongsToMentor()
    {
        return $this->mentoringSlot->belongsToMentor($this->mentor);
    }
    public function test_belongsToMentor_sameMentor_returnTrue()
    {
        $this->assertTrue($this->belongsToMentor());
    }
    public function test_belongsToMentor_differentMentor_returnFalse()
    {
        $this->mentoringSlot->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->assertFalse($this->belongsToMentor());
    }
    
    protected function update()
    {
        $this->schedule->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->mentoringSlot->update($this->getMentoringSlotData());
    }
    public function test_update_updateScheduleAndCapacity()
    {
        $this->update();
        $this->assertSame($this->capacity, $this->mentoringSlot->capacity);
        $schedule = new Schedule($this->getScheduleData());
        $this->assertEquals($schedule, $this->mentoringSlot->schedule);
    }
    public function test_update_notUpcomingSchedule_badRequest()
    {
        $this->startTime = new \DateTimeImmutable('-1 hours');
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Bad Request', 'bad request: mentoring slot must be an upcoming schedule');
    }
    public function test_update_emptyCapacity_badRequest()
    {
        $this->capacity = 0;
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Bad Request', 'bad request: mentoring slot capacity is mandatory');
    }
    public function test_update_hasActiveBooking_forbidden()
    {
        $this->bookedSlot->expects($this->once())
                ->method('isActiveBooking')
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Forbidden', 'bad request: unable to change mentoring slot setting because an active booking is exist');
    }
    public function test_update_updateAnAlreadyPassedMentoring_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Forbidden', 'forbidden: unable to update schedule that already passed');
    }
    
    protected function cancel()
    {
        $this->schedule->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->mentoringSlot->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->cancel();
        $this->assertTrue($this->mentoringSlot->cancelled);
    }
    public function test_cancel_containActiveBooking_cancelAllActiveBooking()
    {
        $this->bookedSlot->expects($this->any())
                ->method('isActiveBooking')
                ->willReturn(true);
        $this->bookedSlot->expects($this->once())
                ->method('cancel');
        $this->cancel();
    }
    public function test_cancel_notUpcomingSchedule_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->cancel();
        }, 'Forbidden', 'forbidden: can only cancel an upcoming schedule');
    }
    
    protected function processReportIn()
    {
        $this->schedule->expects($this->any())
                ->method('isAlreadyPassed')
                ->willReturn(true);
        $this->mentoringSlot->processReportIn($this->containMentorReport, $this->formRecordData, $this->participantRating);
    }
    public function test_processReportIn_forwardReportProcessingToConsultationSetup()
    {
        $this->consultationSetup->expects($this->once())
                ->method('processReportIn')
                ->with($this->containMentorReport, $this->formRecordData, $this->participantRating);
        $this->processReportIn();
    }
    public function test_processReportIn_notAPastMentoring_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isAlreadyPassed')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->processReportIn();
        }, 'Forbidden', 'forbidden: can only process report on past mentoring');
                
    }
    
    protected function isActiveSlotInConflictWith()
    {
        $this->containSchedule->expects($this->any())
                ->method('scheduleInConflictWith')
                ->with($this->mentoringSlot->schedule)
                ->willReturn(true);
        return $this->mentoringSlot->isActiveSlotInConflictWith($this->containSchedule);
    }
    public function test_isActiveSlotInConflictWith_activeWithWithConflictedSchedule_returnTrue()
    {
        $this->assertTrue($this->isActiveSlotInConflictWith());
    }
    public function test_isActiveSlotInConflictWith_noConflict_returnFalse()
    {
        $this->containSchedule->expects($this->once())
                ->method('scheduleInConflictWith')
                ->with($this->mentoringSlot->schedule)
                ->willReturn(false);
        $this->assertFalse($this->isActiveSlotInConflictWith());
    }
    public function test_isActiveSlotInConflictWith_cancelledSlot_returnFalse()
    {
        $this->mentoringSlot->cancelled = true;
        $this->assertFalse($this->isActiveSlotInConflictWith());
    }
    public function test_isActiveSlotInConflictWith_comparedToSameMentoringSlot_returnFalse()
    {
        $this->schedule->expects($this->any())
                ->method('intersectWith')
                ->willReturn(true);
        $this->assertFalse($this->mentoringSlot->isActiveSlotInConflictWith($this->mentoringSlot));
    }
    
    protected function scheduleInConflictWith()
    {
        return $this->mentoringSlot->scheduleInConflictWith($this->otherSchedule);
    }
    public function test_scheduleInConflictWith_returnScheduleIntersectWithComparison()
    {
        $this->schedule->expects($this->once())
                ->method('intersectWith')
                ->with($this->otherSchedule);
        $this->scheduleInConflictWith();
    }
}

class TestableMentoringSlot extends MentoringSlot
{
    public $mentor;
    public $id;
    public $cancelled;
    public $schedule;
    public $capacity;
    public $consultationSetup;
    public $bookedSlots;
}
