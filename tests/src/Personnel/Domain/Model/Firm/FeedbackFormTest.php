<?php

namespace Personnel\Domain\Model\Firm;

use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class FeedbackFormTest extends TestBase
{
    protected $consultationFeedbackForm;
    protected $form;
    
    protected $mentoring, $formRecordData, $participantRating = 6;


    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationFeedbackForm = new TestableFeedbackForm();
        $this->form = $this->buildMockOfClass(Form::class);
        $this->consultationFeedbackForm->form = $this->form;
        
        $this->mentoring = $this->buildMockOfInterface(ContainMentorReport::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_createFormRecord_returnFormRecord()
    {
        $formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->assertInstanceOf(FormRecord::class, $this->consultationFeedbackForm->createFormRecord($id = 'id', $formRecordData));
    }
    
    protected function processReportIn()
    {
        $this->consultationFeedbackForm->processReportIn(
                $this->mentoring, $this->formRecordData, $this->participantRating);
    }
    public function test_processReportIn_submitMentoringReportInMentoring()
    {
        $this->mentoring->expects($this->once())
                ->method('processReport')
                ->with($this->form, $this->formRecordData, $this->participantRating);
        $this->processReportIn();
    }

}

class TestableFeedbackForm extends FeedbackForm
{
    public $form;
    
    public function __construct()
    {
        parent::__construct();
    }
}
