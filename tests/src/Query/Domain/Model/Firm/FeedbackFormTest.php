<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Shared\Form;
use Query\Domain\Model\Shared\FormRecord;
use Tests\TestBase;

class FeedbackFormTest extends TestBase
{
    protected $feedbackForm;
    protected $form;
    protected $formRecord;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackForm = new TestableFeedbackForm();
        
        $this->form = $this->buildMockOfClass(Form::class);
        $this->feedbackForm->form = $this->form;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_toArrayOfSummaryTableHeader_returnFormToArrayOfSummaryTableHeaderResult()
    {
        $this->form->expects($this->once())
                ->method('toArrayOfSummaryTableHeader');
        $this->feedbackForm->toArrayOfSummaryTableHeader();
    }
    
    public function test_generateSummaryTableEntryFromRecord_returnFormGenerateSummaryTableEntryFromRecordResult()
    {
        $this->form->expects($this->once())
                ->method('generateSummaryTableEntryFromRecord')
                ->with($this->formRecord);
        $this->feedbackForm->generateSummaryTableEntryFromRecord($this->formRecord);
    }
}

class TestableFeedbackForm extends FeedbackForm
{
    public $firm;
    public $id = 'feedback-form-id';
    public $form;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
