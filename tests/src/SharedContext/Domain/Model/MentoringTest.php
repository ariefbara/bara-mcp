<?php

namespace SharedContext\Domain\Model;

use SharedContext\Domain\Model\Mentoring\MentorReport;
use SharedContext\Domain\Model\Mentoring\ParticipantReport;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class MentoringTest extends TestBase
{
    protected $mentoring;
    protected $mentorReport;
    protected $participantReport;
    
    protected $id = 'newId';
    protected $form, $formRecordData, $rating = 4;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoring = new TestableMentoring('id');
        
        $this->mentorReport = $this->buildMockOfClass(MentorReport::class);
        $this->participantReport = $this->buildMockOfClass(ParticipantReport::class);
        
        $this->form = $this->buildMockOfClass(Form::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function construct()
    {
        return new TestableMentoring($this->id);
    }
    public function test_construct_setProperties()
    {
        $mentoring = $this->construct();
        $this->assertSame($this->id, $mentoring->id);
    }
    
    protected function submitMentorReport()
    {
        $this->mentoring->submitMentorReport($this->form, $this->formRecordData, $this->rating);
    }
    public function test_submitMentorReport_appendMentorReport()
    {
        $this->submitMentorReport();
        $this->assertInstanceOf(MentorReport::class, $this->mentoring->mentorReport);
    }
    public function test_submitMentorReport_containMentorReport_updateExistingMentorReport()
    {
        $this->mentoring->mentorReport = $this->mentorReport;
        $this->mentorReport->expects($this->once())
                ->method('update')
                ->with($this->rating, $this->form, $this->formRecordData);
        $this->submitMentorReport();
    }
    public function test_submitMentorReport_containMentorReport_prependSetNewReport()
    {
        $this->mentoring->mentorReport = $this->mentorReport;
        $this->submitMentorReport();
        $this->assertSame($this->mentorReport, $this->mentoring->mentorReport);
    }
    
    protected function submitParticipantReport()
    {
        $this->mentoring->submitParticipantReport($this->form, $this->formRecordData, $this->rating);
    }
    public function test_submitParticipantReport_appendParticipantReport()
    {
        $this->submitParticipantReport();
        $this->assertInstanceOf(ParticipantReport::class, $this->mentoring->participantReport);
    }
    public function test_submitParticipantReport_containParticipantReport_updateExistingParticipantReport()
    {
        $this->mentoring->participantReport = $this->participantReport;
        $this->participantReport->expects($this->once())
                ->method('update')
                ->with($this->rating, $this->form, $this->formRecordData);
        $this->submitParticipantReport();
    }
    public function test_submitParticipantReport_containParticipantReport_prependSetNewReport()
    {
        $this->mentoring->participantReport = $this->participantReport;
        $this->submitParticipantReport();
        $this->assertSame($this->participantReport, $this->mentoring->participantReport);
    }
}

class TestableMentoring extends Mentoring
{
    public $id;
    public $participantReport;
    public $mentorReport;
}
