<?php

namespace Participant\Domain\Model\Participant;

use DateTimeImmutable;
use Participant\Domain\DependencyModel\Firm\Program\Consultant;
use Participant\Domain\DependencyModel\Firm\Program\ConsultationSetup;
use Participant\Domain\Model\Participant;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\Schedule;
use SharedContext\Domain\ValueObject\ScheduleData;
use Tests\TestBase;

class DeclaredMentoringTest extends TestBase
{
    protected $participant;
    protected $mentor;
    protected $consultationSetup;
    protected $declaredMentoring, $declaredStatus, $mentoring;
    protected $id = 'newId', $startTime, $endTime, $mediaType = 'new media type', $location = 'new location';
    protected $newDeclaredStatus;
    protected $formRecordData, $mentorRating = 777;
    protected $form;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->mentor = $this->buildMockOfClass(Consultant::class);
        $this->consultationSetup = $this->buildMockOfClass(ConsultationSetup::class);
        
        $scheduleData = new ScheduleData(
                new DateTimeImmutable('-49 hours'), new DateTimeImmutable('-48 hours'), 'media type', 'location');
        $this->declaredMentoring = new TestableDeclaredMentoring($this->participant, 'id', $this->mentor, $this->consultationSetup, $scheduleData);
        
        $this->declaredStatus = $this->buildMockOfClass(DeclaredMentoringStatus::class);
        $this->declaredMentoring->declaredStatus = $this->declaredStatus;
        
        $this->mentoring = $this->buildMockOfClass(Mentoring::class);
        $this->declaredMentoring->mentoring = $this->mentoring;
        
        $this->startTime = new DateTimeImmutable('-25 hours');
        $this->endTime = new DateTimeImmutable('-24 hours');
        
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
                $this->participant, $this->id, $this->mentor, $this->consultationSetup, $this->getScheduleData());
    }
    public function test_construct_setProperties()
    {
        $declaration = $this->construct();
        $this->assertSame($this->participant, $declaration->participant);
        $this->assertSame($this->id, $declaration->id);
        $this->assertSame($this->mentor, $declaration->mentor);
        $this->assertSame($this->consultationSetup, $declaration->consultationSetup);
        
        $declaredStatus = new DeclaredMentoringStatus(DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT);
        $this->assertEquals($declaredStatus, $declaration->declaredStatus);
        
        $schedule = new Schedule($this->getScheduleData());
        $this->assertEquals($schedule, $declaration->schedule);
        
        $mentoring = new Mentoring($this->id);
        $this->assertEquals($mentoring, $declaration->mentoring);
    }
    public function test_construct_notPastSchedule_badRequest()
    {
        $this->startTime = new DateTimeImmutable('+24 hours');
        $this->endTime = new DateTimeImmutable('+25 hours');
        
        $this->assertRegularExceptionThrowed(function() {
            $this->construct();
        }, 'Bad Request', 'bad request: can only declared past mentoring event');
    }
    
    protected function assertManageableyByParticipant()
    {
        $this->declaredMentoring->assertManageableByParticipant($this->participant);
    }
    public function test_assertManageableByParticipant_sameParticipant_void()
    {
        $this->assertManageableyByParticipant();
        $this->markAsSuccess();
    }
    public function test_assertManageableByParticipant_differentParticipant_forbidden()
    {
        $this->declaredMentoring->participant = $this->buildMockOfClass(Participant::class);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableyByParticipant();
        }, 'Forbidden', 'forbidden: can only manage owned declaration');
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
    public function test_update_notPastEvent_forbidden()
    {
        $this->startTime = new DateTimeImmutable('+24 hours');
        $this->endTime = new DateTimeImmutable('+25 hours');
        
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Bad Request', 'bad request: can only declared past mentoring event');
    }
    public function test_update_notParticipantDeclaredState_forbidden()
    {
        $this->declaredStatus->expects($this->once())
                ->method('statusEquals')
                ->with(DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->update();
        }, 'Forbidden', 'forbidden: can only update declaration in declared by participant state');
    }
    
    protected function cancel()
    {
        $this->declaredMentoring->cancel();
    }
    public function test_cancel_setCancelled()
    {
        $this->declaredStatus->expects($this->once())
                ->method('cancelParticipantDeclaration')
                ->willReturn($this->newDeclaredStatus);
        $this->cancel();
        $this->assertSame($this->newDeclaredStatus, $this->declaredMentoring->declaredStatus);
    }
    
    protected function approveMentorDeclaration()
    {
        $this->declaredMentoring->approveMentorDeclaration();
    }
    public function test_approveMentorDeclaration_setApproved()
    {
        $this->declaredStatus->expects($this->once())
                ->method('approveMentorDeclaration')
                ->willReturn($this->newDeclaredStatus);
        $this->approveMentorDeclaration();
        $this->assertSame($this->newDeclaredStatus, $this->declaredMentoring->declaredStatus);
    }
    
    protected function denyMentorDeclaration()
    {
        $this->declaredMentoring->denyMentorDeclaration();
    }
    public function test_denyMentorDeclaration_setApproved()
    {
        $this->declaredStatus->expects($this->once())
                ->method('denyMentorDeclaration')
                ->willReturn($this->newDeclaredStatus);
        $this->denyMentorDeclaration();
        $this->assertSame($this->newDeclaredStatus, $this->declaredMentoring->declaredStatus);
    }
    
    protected function submitReport()
    {
        $this->declaredStatus->expects($this->any())
                ->method('statusIn')
                ->willReturn(true);
        $this->declaredMentoring->submitReport($this->formRecordData, $this->mentorRating);
    }
    public function test_submitReport_tellConsultationSetupToProcessReport()
    {
        $this->consultationSetup->expects($this->once())
                ->method('processReportIn')
                ->with($this->declaredMentoring, $this->formRecordData, $this->mentorRating);
        $this->submitReport();
    }
    public function test_submitReport_notInReportableStatus_forbidden()
    {
        $reportableStatusList = [
            DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
            DeclaredMentoringStatus::APPROVED_BY_MENTOR,
            DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
        ];
        $this->declaredStatus->expects($this->once())
                ->method('statusIn')
                ->with($reportableStatusList)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->submitReport();
        }, 'Forbidden', 'forbidden: declaration not in reportable state');
    }
    
    protected function processReport()
    {
        $this->declaredMentoring->processReport($this->form, $this->formRecordData, $this->mentorRating);
    }
    public function test_processReport_submitMentoringParticipantReport()
    {
        $this->mentoring->expects($this->once())
                ->method('submitParticipantReport')
                ->with($this->form, $this->formRecordData, $this->mentorRating);
        $this->processReport();
    }
}

class TestableDeclaredMentoring extends DeclaredMentoring
{
    public $participant;
    public $id;
    public $mentor;
    public $consultationSetup;
    public $declaredStatus;
    public $schedule;
    public $mentoring;
}
