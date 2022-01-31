<?php

namespace Participant\Domain\Model\Participant\MentoringRequest;

use Participant\Domain\Model\Participant;
use Participant\Domain\Model\Participant\MentoringRequest;
use SharedContext\Domain\Model\Mentoring;
use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class NegotiatedMentoringTest extends TestBase
{
    protected $mentoringRequest;
    protected $negotiatedMentoring, $mentoring;
    protected $id = 'newId';
    protected $participant;
    protected $mentorRating = 5, $formRecordData, $form;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mentoringRequest = $this->buildMockOfClass(MentoringRequest::class);
        $this->negotiatedMentoring = new TestableNegotiatedMentoring($this->mentoringRequest, 'id');
        $this->mentoring = $this->buildMockOfClass(Mentoring::class);
        $this->negotiatedMentoring->mentoring = $this->mentoring;
        $this->participant = $this->buildMockOfClass(Participant::class);
        
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
        $this->assertSame($this->mentoringRequest, $negotiatedMentoring->mentoringRequest);
        $this->assertSame($this->id, $negotiatedMentoring->id);
        $this->assertInstanceOf(Mentoring::class, $negotiatedMentoring->mentoring);
    }
    
    protected function assertManageableByParticipant()
    {
        $this->mentoringRequest->expects($this->any())
                ->method('belongsToParticipant')
                ->with($this->participant)
                ->willReturn(true);
        $this->negotiatedMentoring->assertManageableByParticipant($this->participant);
    }
    public function test_assertManageableByParticipant_mentoringRequestBelongsToSameParticipant_void()
    {
        $this->assertManageableByParticipant();
        $this->markAsSuccess();
    }
    public function test_assertManageableByParticipant_mentoringRequestDoesntBelongsToSameParticipant_void()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('belongsToParticipant')
                ->with($this->participant)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function() {
            $this->assertManageableByParticipant();
        }, 'Forbidden', 'forbidden: can only manage owned negotiated mentoring');
    }
    
    protected function submitReport()
    {
        $this->negotiatedMentoring->submitReport($this->mentorRating, $this->formRecordData);
    }
    public function test_submitReport_tellMentoringRequestToProcessReport()
    {
        $this->mentoringRequest->expects($this->once())
                ->method('processReportInMentoring')
                ->with($this->negotiatedMentoring, $this->formRecordData, $this->mentorRating);
        $this->submitReport();
    }
    
    protected function processReport()
    {
        $this->negotiatedMentoring->processReport($this->form, $this->formRecordData, $this->mentorRating);
    }
    public function test_processReport_submitMentoringParticipantReport()
    {
        $this->mentoring->expects($this->once())
                ->method('submitParticipantReport')
                ->with($this->form, $this->formRecordData, $this->mentorRating);
        $this->processReport();
    }
}

class TestableNegotiatedMentoring extends NegotiatedMentoring
{
    public $mentoringRequest;
    public $id;
    public $mentoring;
}
