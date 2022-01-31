<?php

namespace Participant\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class FeedbackFormTest extends TestBase
{
    protected $feedbackForm;
    protected $form;
    
    protected $formRecordId = 'formRecordId', $formRecordData;
    protected $mentoring, $mentorRating = 6;


    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackForm = new TestableFeedbackForm();
        
        $this->form = $this->buildMockOfClass(Form::class);
        $this->feedbackForm->form = $this->form;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->mentoring = $this->buildMockOfInterface(IContainParticipantReport::class);
    }
    public function test_createFormRecord_returnFormRecord()
    {
        $formRecord = new FormRecord($this->form, $this->formRecordId, $this->formRecordData);
        $this->assertEquals($formRecord, $this->feedbackForm->createFormRecord($this->formRecordId, $this->formRecordData));
    }
    
    protected function processReportIn()
    {
        $this->feedbackForm->processReportIn($this->mentoring, $this->formRecordData, $this->mentorRating);
    }
    public function test_processReportIn_processMentoringReport()
    {
        $this->mentoring->expects($this->once())
                ->method('processReport')
                ->with($this->form, $this->formRecordData, $this->mentorRating);
        $this->processReportIn();
    }
}

class TestableFeedbackForm extends FeedbackForm
{
    public $firmId;
    public $id;
    public $form;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
