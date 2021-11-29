<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\MentoringRequest\NegotiatedMentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;
use Tests\TestBase;

class MentoringRequestTest extends TestBase
{
    protected $endTime;
    protected $participant;
    protected $mentor;
    protected $consultationSetup;
    protected $mentoringRequest, $schedule, $requestStatus;
    protected $id = 'newId', $startTime, $mediaType = 'online', $location = 'meet.google.com';
    protected $otherMentoring;
    protected $otherSchedule;
    protected $negotiatedMentoring;
    protected $formRecordData, $mentorRating = 5;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->mentor = $this->buildMockOfClass(Consultant::class);
        
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $this->endTime = new DateTimeImmutable('+72 hours');
        $this->consultationSetup->expects($this->any())
                ->method('calculateScheduleEndTime')
                ->willReturn($this->endTime);
        
        $mentoringRequestData = new MentoringRequestData(new DateTimeImmutable('+24 hours'), 'media type', 'location');
        $this->mentoringRequest = new TestableMentoringRequest(
                $this->participant, 'id', $this->mentor, $this->consultationSetup, $mentoringRequestData);
        
        $this->schedule = $this->buildMockOfClass(Schedule::class);
        $this->mentoringRequest->schedule = $this->schedule;
        
        $this->requestStatus = $this->buildMockOfClass(MentoringRequestStatus::class);
        $this->mentoringRequest->requestStatus = $this->requestStatus;
        
        $this->startTime = new DateTimeImmutable('+48 hours');
        $this->otherMentoring = $this->buildMockOfInterface(ContainSchedule::class);
        $this->otherSchedule = $this->buildMockOfClass(Schedule::class);
        $this->negotiatedMentoring = $this->buildMockOfClass(NegotiatedMentoring::class);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function getMentoringRequestData()
    {
        return new MentoringRequestData($this->startTime, $this->mediaType, $this->location);
    }
    
    protected function construct()
    {
        return new TestableMentoringRequest(
                $this->participant, $this->id, $this->mentor, $this->consultationSetup, $this->getMentoringRequestData());
    }
    public function test_construct_setProperties()
    {
        $mentoringRequest = $this->construct();
        $this->assertSame($this->participant, $mentoringRequest->participant);
        $this->assertSame($this->id, $mentoringRequest->id);
        $this->assertSame($this->mentor, $mentoringRequest->mentor);
        $this->assertSame($this->consultationSetup, $mentoringRequest->consultationSetup);
        
        $requestStatus = new MentoringRequestStatus(MentoringRequestStatus::REQUESTED);
        $this->assertEquals($requestStatus, $mentoringRequest->requestStatus);
        
        $schedule = new Schedule(new ScheduleData($this->startTime, $this->endTime, $this->mediaType, $this->location));
        $this->assertEquals($schedule, $mentoringRequest->schedule);
    }
    public function test_construct_notUpcomingSchedule_badRequest()
    {
        $this->startTime = new DateTimeImmutable('-24 hours');
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: can only request upcomming mentoring schedule');
    }
    public function test_construct_assertScheduleNotInConflictWithParticipantScheduleOrPotentialSchedule()
    {
        $this->participant->expects($this->once())
                ->method('assertNoConflictWithScheduledOrPotentialSchedule');
        $this->construct();
    }
    
    protected function assertManageableByParticipant()
    {
        $this->mentoringRequest->assertManageableByParticipant($this->participant);
    }
    public function test_assertManageableByParticipant_unconcludedRequestOfSameParticipant_void()
    {
        $this->assertManageableByParticipant();
        $this->markAsSuccess();
    }
    public function test_assertManageableByParticipant_differentParticipant_forbidden()
    {
        $this->mentoringRequest->participant = $this->buildMockOfClass(Participant::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByParticipant();
        }, 'Forbidden', 'forbidden: can only managed owned mentoring request');
    }
    
    protected function update()
    {
        $this->schedule->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->requestStatus->expects($this->any())
                ->method('isConcluded')
                ->willReturn(false);
        $this->mentoringRequest->update($this->getMentoringRequestData());
    }
    public function test_update_updateSchedule()
    {
        $this->update();
        $schedule = new Schedule(new ScheduleData($this->startTime, $this->endTime, $this->mediaType, $this->location));
        $this->assertEquals($schedule, $this->mentoringRequest->schedule);
    }
    public function test_update_notUpcomingSchedule_badRequest()
    {
        $this->startTime = new DateTimeImmutable('-24 hours');
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Bad Request', 'bad request: can only request upcomming mentoring schedule');
    }
    public function test_update_assertScheduleNotInConflictWithParticipantScheduleOrPotentialSchedule()
    {
        $this->participant->expects($this->once())
                ->method('assertNoConflictWithScheduledOrPotentialSchedule');
        $this->update();
    }
    public function test_update_updateStatus()
    {
        $this->update();
        $requestStatus = new MentoringRequestStatus(MentoringRequestStatus::REQUESTED);
        $this->assertEquals($requestStatus , $this->mentoringRequest->requestStatus);
    }
    public function test_update_concludedRequest_forbidden()
    {
        $this->requestStatus->expects($this->once())
                ->method('isConcluded')
                ->willReturn(true);
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Forbidden', 'forbidden: can only update active mentoring request');
    }
    
    protected function cancel()
    {
        $this->schedule->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->mentoringRequest->cancel();
    }
    public function test_cancel_updateStatus()
    {
        $this->requestStatus->expects($this->once())
                ->method('cancel')
                ->willReturn($requestStatus = new MentoringRequestStatus(MentoringRequestStatus::CANCELLED));
        $this->cancel();
        $this->assertSame($requestStatus, $this->mentoringRequest->requestStatus);
    }
    
    protected function accept()
    {
        $this->schedule->expects($this->any())
                ->method('isUpcoming')
                ->willReturn(true);
        $this->mentoringRequest->accept();
    }
    public function test_accept_updateStatus()
    {
        $this->requestStatus->expects($this->once())
                ->method('accept')
                ->willReturn($requestStatus = new MentoringRequestStatus(MentoringRequestStatus::CANCELLED));
        $this->accept();
        $this->assertSame($requestStatus, $this->mentoringRequest->requestStatus);
    }
    public function test_accept_notUpcomingSchedule_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isUpcoming')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->accept();
        }, 'Forbidden', 'forbidden: can only accept upcoming schedule');
    }
    public function test_accept_assertScheduleNotInConflictWithParticipantScheduleOrPotentialSchedule()
    {
        $this->participant->expects($this->once())
                ->method('assertNoConflictWithScheduledOrPotentialSchedule')
                ->with($this->mentoringRequest);
        $this->accept();
    }
    public function test_accept_appendNegotiatedMentoring()
    {
        $this->accept();
        $this->assertInstanceOf(NegotiatedMentoring::class, $this->mentoringRequest->negotiatedMentoring);
    }
    
    protected function inConflictWith()
    {
        $this->otherMentoring->expects($this->any())
                ->method('scheduleIntersectWith')
                ->with($this->schedule)
                ->willReturn(true);
        $this->requestStatus->expects($this->any())
                ->method('isScheduledOrPotentialSchedule')
                ->willReturn(true);
        $this->requestStatus->expects($this->any())
                ->method('isScheduledOrPotentialSchedule')
                ->willReturn(true);
        return $this->mentoringRequest->aScheduledOrPotentialScheduleInConflictWith($this->otherMentoring);
    }
    public function test_inConflictWith_scheduleIntersectWithOtherMentoring_returnTrue()
    {
        $this->assertTrue($this->inConflictWith());
    }
    public function test_inConflictWith_scheduleNotIntersectWithOtherMentoring_returnFalse()
    {
        $this->otherMentoring->expects($this->once())
                ->method('scheduleIntersectWith')
                ->with($this->schedule)
                ->willReturn(false);
        $this->assertFalse($this->inConflictWith());
    }
    public function test_inConflictWith_sameRequest_returnFalse()
    {
        $this->assertFalse($this->mentoringRequest->aScheduledOrPotentialScheduleInConflictWith($this->mentoringRequest));
    }
    public function test_inConflictWith_notAScheduledOrPotentialSchedule_returnFalse()
    {
        $this->requestStatus->expects($this->once())
                ->method('isScheduledOrPotentialSchedule')
                ->willReturn(false);
        $this->assertFalse($this->inConflictWith());
    }
    
    protected function scheduleIntersectWith()
    {
        return $this->mentoringRequest->scheduleIntersectWith($this->otherSchedule);
    }
    public function test_schduleIntersectWith_returnScheduleIntersectWithComparison()
    {
        $this->schedule->expects($this->once())
                ->method('intersectWith')
                ->with($this->otherSchedule)
                ->willReturn(true);
        $this->assertTrue($this->scheduleIntersectWith());
    }
    
    protected function belongsToParticipant()
    {
        return $this->mentoringRequest->belongsToParticipant($this->participant);
    }
    public function test_belongsToParticipant_sameParticipant_returnTrue()
    {
        $this->assertTrue($this->belongsToParticipant());
    }
    public function test_belongsToParticipant_differentParticipant_returnFalse()
    {
        $this->mentoringRequest->participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->belongsToParticipant());
    }
    
    protected function processReportInMentoring()
    {
        $this->schedule->expects($this->any())
                ->method('isAlreadyPassed')
                ->willReturn(true);
        $this->mentoringRequest->processReportInMentoring($this->negotiatedMentoring, $this->formRecordData, $this->mentorRating);
    }
    public function test_processReportInMentoring_tellConsultationSetupToProcessReport()
    {
        $this->consultationSetup->expects($this->once())
                ->method('processReportIn')
                ->with($this->negotiatedMentoring, $this->formRecordData, $this->mentorRating);
        $this->processReportInMentoring();
    }
    public function test_processReportIn_notPastSchedule_forbidden()
    {
        $this->schedule->expects($this->once())
                ->method('isAlreadyPassed')
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->processReportInMentoring();
        }, 'Forbidden', 'forbidden: can only process report on past mentoring');
    }
}

class TestableMentoringRequest extends MentoringRequest
{
    public $participant;
    public $id;
    public $requestStatus;
    public $schedule;
    public $mentor;
    public $consultationSetup;
    public $negotiatedMentoring;
    
}
