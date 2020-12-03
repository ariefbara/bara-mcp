<?php

namespace User\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecordData
};
use Tests\TestBase;

class FeedbackFormTest extends TestBase
{
    protected $form;
    protected $feedbackForm;
    protected $formRecordId = "formRecordId", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->form = $this->buildMockOfClass(Form::class);
        
        $this->feedbackForm = new TestableFeedbackForm();
        $this->feedbackForm->form = $this->form;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    public function test_createFormRecord_returnFormRecordCreatedInForm()
    {
        $this->form->expects($this->once())
                ->method("createFormRecord")
                ->with($this->formRecordId, $this->formRecordData);
        $this->feedbackForm->createFormRecord($this->formRecordId, $this->formRecordData);
    }
}

class TestableFeedbackForm extends FeedbackForm
{
    public $firm;
    public $id;
    public $form;
    public $removed = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
