<?php

namespace Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\MentoringRequest;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class NegotiatedMentoringTest extends TestBase
{
    protected $mentoringRequest;
    protected $negotiatedMentoring, $mentoring;
    protected $id = 'newId';
    protected $mentor;
    protected $formRecordData, $participantRating = 77;
    protected $form;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoringRequest = $this->buildMockOfClass(MentoringRequest::class);
        $this->negotiatedMentoring = new TestableNegotiatedMentoring($this->mentoringRequest, 'id');
        
        $this->mentoring = $this->buildMockOfClass(Mentoring::class);
        $this->negotiatedMentoring->mentoring = $this->mentoring;
        
        $this->mentor = $this->buildMockOfClass(ProgramConsultant::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->form = $this->buildMockOfClass(Form::class);
    }
    
    protected function construct()
    {
        return new TestableNegotiatedMentoring($this->mentoringRequest, $this->id);
    }
    public function test_construct_setProperties()
    {
        $negotiatedMentoring = $this->construct();
        $this->assertEquals($this->mentoringRequest, $negotiatedMentoring->mentoringRequest);
        $this->assertEquals($this->id, $negotiatedMentoring->id);
        $this->assertInstanceOf(Mentoring::class, $negotiatedMentoring->mentoring);
    }
    
    protected function assertBelongsToMentor()
    {
        $this->mentoringRequest->expects($this->any())
                ->method('belongsToMentor')
                ->willReturn(true);
        $this->negotiatedMentoring->assertBelongsToMentor($this->mentor);
    }
    public function test_assertBelongsToMentor_mentoringRequestBelongsToMentor_void()
    {
        $this->assertBelongsToMentor();
        $this->markAsSuccess();
    }
    public function test_assertBelongsToMentor_mentoringRequestDoesntBelongsToMentor_forbidden()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('belongsToMentor')
                ->with($this->mentor)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertBelongsToMentor();
        }, 'Forbidden', 'forbidden: can only manage owned negotiated mentoring');
    }
    
    protected function submitReport()
    {
        $this->negotiatedMentoring->submitReport($this->formRecordData, $this->participantRating);
    }
    public function test_submitReport_tellMentoringRequestToProcessReport()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('processMentoringReport')
                ->with($this->negotiatedMentoring, $this->formRecordData, $this->participantRating);
        $this->submitReport();
    }
    
    protected function processReport()
    {
        $this->negotiatedMentoring->processReport($this->form, $this->formRecordData, $this->participantRating);
    }
    public function test_processReport_submitMentoringMentorReport()
    {
        $this->mentoring->expects($this->once())
                ->method('submitMentorReport')
                ->with($this->form, $this->formRecordData, $this->participantRating);
        $this->processReport();
    }
    
}

class TestableNegotiatedMentoring extends NegotiatedMentoring
{
    public $mentoringRequest;
    public $id;
    public $mentoring;
}
