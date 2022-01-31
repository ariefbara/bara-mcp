<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;

use DateTimeImmutable;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Program\ConsultationSetup;
use Personnel\Domain\Model\Firm\Program\Participant;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;
use Tests\TestBase;

class DeclaredMentoringTest extends TestBase
{
    protected $mentor;
    protected $participant;
    protected $consultationSetup;
    protected $declaredMentoring, $mentoring, $declaredStatus;
    protected $id = 'newId', $startTime, $endTime, $mediaType = 'new media type', $location = 'new location';
    protected $newDeclaredStatus;
    protected $formRecordData, $participantRating = 55;
    protected $form;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        $startTime = new DateTimeImmutable('-25 hours');
        $endTime = new DateTimeImmutable('-24 hours');
        $scheduleData = new ScheduleData($startTime, $endTime, 'media type', 'location');
        
        $this->declaredMentoring = new TestableDeclaredMentoring(
                $this->mentor, 'id', $this->participant, $this->consultationSetup, $scheduleData);
        $this->mentoring = $this->buildMockOfClass(Mentoring::class);
        $this->declaredMentoring->mentoring = $this->mentoring;
        $this->declaredStatus = $this->buildMockOfClass(DeclaredMentoringStatus::class);
        $this->declaredMentoring->declaredStatus = $this->declaredStatus;
        
        $this->startTime = new DateTimeImmutable('-49 hours');
        $this->endTime = new DateTimeImmutable('-48 hours');
        
        $this->newDeclaredStatus = $this->buildMockOfClass(DeclaredMentoringStatus::class);
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->form = $this->buildMockOfClass(Form::class);
    }
    
    protected function getScheduleData()
    {
        return new ScheduleData($this->startTime, $this->endTime, $this->mediaType, $this->location);
    }
    
    protected function construct()
    {
        return new TestableDeclaredMentoring(
                $this->mentor, $this->id, $this->participant, $this->consultationSetup, $this->getScheduleData());
    }
    public function test_construct_setProperties()
    {
        $declaredMentoring = $this->construct();
        $this->assertEquals($this->mentor, $declaredMentoring->mentor);
        $this->assertEquals($this->id, $declaredMentoring->id);
        $this->assertEquals($this->participant, $declaredMentoring->participant);
        $this->assertEquals($this->consultationSetup, $declaredMentoring->consultationSetup);
        
        $status = new DeclaredMentoringStatus(DeclaredMentoringStatus::DECLARED_BY_MENTOR);
        $this->assertEquals($status, $declaredMentoring->declaredStatus);
        
        $schedule = new Schedule($this->getScheduleData());
        $this->assertEquals($schedule, $declaredMentoring->schedule);
        
        $this->assertInstanceOf(Mentoring::class, $declaredMentoring->mentoring);
    }
    public function test_construct_notPastSchedule_badRequest()
    {
        $this->startTime = new DateTimeImmutable('-1 hours');
        $this->endTime = new DateTimeImmutable('+2 hours');
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: declared mentoring must be a past event');
    }
    
    protected function assertManageableByMentor()
    {
        $this->declaredMentoring->assertManageableByMentor($this->mentor);
    }
    public function test_assertManageableByMentor_sameMentor_void()
    {
        $this->assertManageableByMentor();
        $this->markAsSuccess();
    }
    public function test_assertManageableByMentor_differentMentor_forbidden()
    {
        $this->declaredMentoring->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByMentor();
        }, 'Forbidden', 'forbidden: can only manage owned asset');
    }
    
    protected function update()
    {
        $this->declaredStatus->expects($this->any())
                ->method('statusEquals')
                ->willReturn(true);
        $this->declaredMentoring->update($this->getScheduleData());
    }
    public function test_update_updateSchedule()
    {
        $this->update();
        $schedule = new Schedule($this->getScheduleData());
        $this->assertEquals($schedule, $this->declaredMentoring->schedule);
    }
    public function test_update_notPastSchedule_badRequest()
    {
        $this->startTime = new DateTimeImmutable('-1 hours');
        $this->endTime = new DateTimeImmutable('+2 hours');
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Bad Request', 'bad request: declared mentoring must be a past event');
    }
    public function test_update_notDeclaredByMentor_forbidden()
    {
        $this->declaredStatus->expects($this->once())
                ->method('statusEquals')
                ->with(DeclaredMentoringStatus::DECLARED_BY_MENTOR)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Forbidden', 'forbidden: can only update declaration in declared by mentor state');
    }
    
    protected function cancel()
    {
        $this->declaredMentoring->cancel();
    }
    public function test_cancel_setCancelledStatus()
    {
        $this->declaredStatus->expects($this->once())
                ->method('cancelMentorDeclaration')
                ->willReturn($this->newDeclaredStatus);
        $this->cancel();
        $this->assertSame($this->newDeclaredStatus, $this->declaredMentoring->declaredStatus);
    }
    
    protected function approveParticipantDeclaration()
    {
        $this->declaredMentoring->approveParticipantDeclaration();
    }
    public function test_approveParticipantDeclaration_setApproveStatus()
    {
        $this->declaredStatus->expects($this->once())
                ->method('approveParticipantDeclaration')
                ->willReturn($this->newDeclaredStatus);
        $this->approveParticipantDeclaration();
        $this->assertSame($this->newDeclaredStatus, $this->declaredMentoring->declaredStatus);
    }
    
    protected function denyParticipantDeclaration()
    {
        $this->declaredMentoring->denyParticipantDeclaration();
    }
    public function test_denyParticipantDeclaration_setDenyStatus()
    {
        $this->declaredStatus->expects($this->once())
                ->method('denyParticipantDeclaration')
                ->willReturn($this->newDeclaredStatus);
        $this->denyParticipantDeclaration();
        $this->assertSame($this->newDeclaredStatus, $this->declaredMentoring->declaredStatus);
    }
    
    protected function submitReport()
    {
        $this->declaredStatus->expects($this->any())
                ->method('statusIn')
                ->willReturn(true);
        $this->declaredMentoring->submitReport($this->formRecordData, $this->participantRating);
    }
    public function test_submitReport_tellConsultationSetupToProcessReport()
    {
        $this->consultationSetup->expects($this->once())
                ->method('processReportIn')
                ->with($this->declaredMentoring, $this->formRecordData, $this->participantRating);
        $this->submitReport();
    }
    public function test_submitReport_statusNotDeclaredOrApproved_forbidden()
    {
        $this->declaredStatus->expects($this->once())
                ->method('statusIn')
                ->with([
                        DeclaredMentoringStatus::DECLARED_BY_MENTOR,
                        DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
                        DeclaredMentoringStatus::APPROVED_BY_MENTOR,
                        DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
                ])
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->submitReport();
        }, 'Forbidden', 'forbidden: can only submit report on declared or approved status');
    }
    
    protected function processReport()
    {
        $this->declaredMentoring->processReport($this->form, $this->formRecordData, $this->participantRating);
    }
    public function test_processReport_submitMentorReport()
    {
        $this->mentoring->expects($this->once())
                ->method('submitMentorReport')
                ->with($this->form, $this->formRecordData, $this->participantRating);
        $this->processReport();
    }
}

class TestableDeclaredMentoring extends DeclaredMentoring
{
    public $mentor;
    public $id;
    public $participant;
    public $consultationSetup;
    public $declaredStatus;
    public $schedule;
    public $mentoring;
}
