<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest\NegotiatedMentoring;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;
use Tests\TestBase;

class MentoringRequestTest extends TestBase
{
    protected $participant;
    protected $mentoringRequest, $mentor, $requestStatus, $schedule, $consultationSetup;
    protected $startTime, $mediaType = 'new media type', $location = 'new location';
    protected $endTime;
    protected $containSchedule;
    protected $otherSchedule;
    protected $negotiatedMentoring, $formRecordData, $participantRating = 333;
    //
    protected $id = 'newId';

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->startTime = new DateTimeImmutable('+24 hours');
        $this->endTime = new DateTimeImmutable('+25 hours');
        
        $mentoringRequestData = new MentoringRequestData(new \DateTimeImmutable('+48 hours'), 'media type', 'location');
        $this->mentoringRequest = new TestabledMentoringRequest($this->mentor, 'id', $this->consultationSetup, $this->participant, $mentoringRequestData);
        $this->requestStatus = $this->buildMockOfClass(MentoringRequestStatus::class);
        $this->mentoringRequest->requestStatus = $this->requestStatus;
        $this->schedule = $this->buildMockOfClass(Schedule::class);
        $this->mentoringRequest->schedule = $this->schedule;
        
        $this->containSchedule = $this->buildMockOfInterface(ContainSchedule::class);
        $this->otherSchedule = $this->buildMockOfClass(Schedule::class);
        $this->negotiatedMentoring = $this->buildMockOfClass(NegotiatedMentoring::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function getMentoringRequestData()
    {
        return new MentoringRequestData($this->startTime, $this->mediaType, $this->location);
    }
    
    protected function construct() {
        $this->consultationSetup->expects($this->any())
                ->method('calculateMentoringScheduleEndTimeFrom')
                ->with($this->startTime)
                ->willReturn($this->endTime);
        return new TestabledMentoringRequest(
                $this->mentor, $this->id, $this->consultationSetup, $this->participant, $this->getMentoringRequestData());
    }
    public function test_construct_setProperties() {
        $mentoringRequest = $this->construct();
        $this->assertEquals($this->mentor, $mentoringRequest->mentor);
        $this->assertEquals($this->id, $mentoringRequest->id);
        $this->assertEquals($this->consultationSetup, $mentoringRequest->consultationSetup);
        $this->assertEquals($this->participant, $mentoringRequest->participant);
        
        $requestStatus = new MentoringRequestStatus(MentoringRequestStatus::OFFERED);
        $this->assertEquals($requestStatus, $mentoringRequest->requestStatus);
        
        $schedule = new Schedule(new ScheduleData($this->startTime, $this->endTime, $this->mediaType, $this->location));
        $this->assertEquals($schedule, $mentoringRequest->schedule);
    }
    public function test_construct_notUpcomingSchedule_forbidden() {
        $this->startTime = new \DateTimeImmutable('-24 hours');
        $this->assertRegularExceptionThrowed(fn() => $this->construct(), 'Forbidden','forbidden: can only offer upcoming schedule');
    }
    public function test_construct_assertMentorDoesntHaveConflictingSchedule() {
        $this->mentor->expects($this->once())
                ->method('assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule');
        $this->construct();
    }
    
    protected function belongsToMentor()
    {
        return $this->mentoringRequest->belongsToMentor($this->mentor);
    }
    public function test_belongsToMentor_sameMentor_returnTrue()
    {
        $this->assertTrue($this->belongsToMentor());
    }
    public function test_belongsToMentor_differentMentor_returnFalse()
    {
        $this->mentoringRequest->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->assertFalse($this->belongsToMentor());
    }
    
    protected function assertBelongsToMentor()
    {
        $this->mentoringRequest->assertBelongsToMentor($this->mentor);
    }
    public function test_assertBelongsToMentor_sameMentor_void()
    {
        $this->assertBelongsToMentor();
        $this->markAsSuccess();
    }
    public function test_assertBelongsToMentor_differentMentor_forbidden()
    {
        $this->mentoringRequest->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertBelongsToMentor();
        }, 'Forbidden', 'forbidden: can only managed owned mentoring request');
    }
    
    protected function reject()
    {
        $this->mentoringRequest->reject();
    }
    public function test_reject_setRejectedRequestStatus()
    {
        $this->requestStatus->expects($this->once())
                ->method('reject')
                ->willReturn($rejectedRequestStatus = $this->buildMockOfClass(MentoringRequestStatus::class));
        $this->reject();
        $this->assertSame($rejectedRequestStatus, $this->mentoringRequest->requestStatus);
    }
    
    protected function approve()
    {
        $this->schedule->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->mentoringRequest->approve();
    }
    public function test_approve_setApprovedRequestStatus()
    {
        $this->requestStatus->expects($this->once())
                ->method('approve')
                ->willReturn($approvedRequestStatus = $this->buildMockOfClass(MentoringRequestStatus::class));
        $this->approve();
        $this->assertSame($approvedRequestStatus, $this->mentoringRequest->requestStatus);
    }
    public function test_approve_notUpcomingSchedule_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->approve();
        }, 'Forbidden', 'forbidden: can only approve upcoming schedule');
    }
    public function test_approve_assertScheduleNotConlficWithParticipantsExistingScheduleOrPotentialSchedule()
    {
        $this->mentor->expects($this->once())
                ->method('assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule')
                ->with($this->mentoringRequest);
        $this->approve();
    }
    public function test_approve_appendNegotiatedMentoring()
    {
        $this->approve();
        $this->assertInstanceOf(NegotiatedMentoring::class, $this->mentoringRequest->negotiatedMentoring);
    }
    
    protected function offer()
    {
        $this->consultationSetup->expects($this->any())
                ->method('calculateMentoringScheduleEndTimeFrom')
                ->with($this->startTime)
                ->willReturn($this->endTime);
        $this->mentoringRequest->offer($this->getMentoringRequestData());
    }
    public function test_offer_updateSchedule()
    {
        $this->offer();
        $scheduleData = new ScheduleData($this->startTime, $this->endTime, $this->mediaType, $this->location);
        $schedule = new Schedule($scheduleData);
        $this->assertEquals($schedule, $this->mentoringRequest->schedule);
    }
    public function test_offer_setOfferedRequestStatus()
    {
        $this->requestStatus->expects($this->once())
                ->method('offer')
                ->willReturn($offeredRequestStatus = $this->buildMockOfClass(MentoringRequestStatus::class));
        $this->offer();
        $this->assertSame($offeredRequestStatus, $this->mentoringRequest->requestStatus);
    }
    public function test_offer_offeringNonUpcomingSchedule()
    {
        $this->startTime = new \DateTimeImmutable('-2 hours');
        $this->endTime = new \DateTimeImmutable('-1 hours');
        
        $this->assertRegularExceptionThrowed(function() {
            $this->offer();
        }, 'Forbidden', 'forbidden: can only offer upcoming schedule');
    }
    public function test_offer_assertScheduleNotConlficWithParticipantsExistingScheduleOrPotentialSchedule()
    {
        $this->mentor->expects($this->once())
                ->method('assertScheduleNotInConflictWithExistingScheduleOrPotentialSchedule')
                ->with($this->mentoringRequest);
        $this->offer();
    }
    
    protected function isScheduledOrOfferedRequestInConflictWith()
    {
        $this->containSchedule->expects($this->any())
                ->method('scheduleInConflictWith')
                ->with($this->schedule)
                ->willReturn(true);
        $this->requestStatus->expects($this->any())
                ->method('statusIn')
                ->willReturn(true);
        return $this->mentoringRequest->isScheduledOrOfferedRequestInConflictWith($this->containSchedule);
    }
    public function test_isScheduledOrOfferedRequestInConflictWith_conflictedScheduledOrProposedSchedule_returnTrue()
    {
        $this->assertTrue($this->isScheduledOrOfferedRequestInConflictWith());
    }
    public function test_isScheduledOrOfferedRequestInConflictWith_noConflict_returnFalse()
    {
        $this->containSchedule->expects($this->once())
                ->method('scheduleInConflictWith')
                ->with($this->schedule)
                ->willReturn(false);
        $this->assertFalse($this->isScheduledOrOfferedRequestInConflictWith());
    }
    public function test_isScheduledOrOfferedRequestInConflictWith_statusNotScheduleOrProposed_returnFalse()
    {
        $this->requestStatus->expects($this->any())
                ->method('statusIn')
                ->with([
                        MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT,
                        MentoringRequestStatus::APPROVED_BY_MENTOR,
                        MentoringRequestStatus::OFFERED,
                ])
                ->willReturn(false);
        $this->assertFalse($this->isScheduledOrOfferedRequestInConflictWith());
    }
    public function test_isScheduledOrOfferedRequestInConflictWith_comparedWithSameRequest_returnFalse()
    {
        $this->requestStatus->expects($this->any())
                ->method('statusIn')
                ->willReturn(true);
        $this->schedule->expects($this->any())
                ->method('intersectWith')
                ->willReturn(true);
        $this->assertFalse($this->mentoringRequest->isScheduledOrOfferedRequestInConflictWith($this->mentoringRequest));
    }
    
    protected function scheduleInConflictWith()
    {
        return $this->mentoringRequest->scheduleInConflictWith($this->otherSchedule);
    }
    public function test_scheduleInConflictWith_returnScheduleIntersectWithResult()
    {
        $this->schedule->expects($this->once())
                ->method('intersectWith')
                ->with($this->otherSchedule);
        $this->scheduleInConflictWith();
    }
    
    protected function processMentoringReport()
    {
        $this->schedule->expects($this->any())
                ->method('isAlreadyPassed')
                ->willReturn(true);
        $this->mentoringRequest->processMentoringReport(
                $this->negotiatedMentoring, $this->formRecordData, $this->participantRating);
    }
    public function test_processMentoringReport_tellConsultationSetupToProcessReport()
    {
        $this->consultationSetup->expects($this->once())
                ->method('processReportIn')
                ->with($this->negotiatedMentoring, $this->formRecordData, $this->participantRating);
        $this->processMentoringReport();
    }
    public function test_processMentoringReport_notPastRequest_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isAlreadyPassed')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->processMentoringReport();
        }, 'Forbidden', 'forbidden: can only submit report on past mentoring');
    }
    
}

class TestabledMentoringRequest extends MentoringRequest {
    public $mentor;
    public $id = 'mentoringRequestId';
    public $participant;
    public $requestStatus;
    public $schedule;
    public $consultationSetup;
    public $negotiatedMentoring;
}
