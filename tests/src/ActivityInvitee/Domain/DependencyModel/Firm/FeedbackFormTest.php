<?php

namespace ActivityInvitee\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecordData
};
use Tests\TestBase;

class FeedbackFormTest extends TestBase
{
    protected $feedbackForm;
    protected $form;
    protected $formRecordId = "formRecordId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackForm = new TestableFeedbackForm();
        $this->form = $this->buildMockOfClass(Form::class);
        $this->feedbackForm->form = $this->form;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_createFormRecord_returnFormsCreateFormRecordResult()
    {
        $this->form->expects($this->once())
                ->method("createFormRecord")
                ->with($this->formRecordId, $this->formRecordData);
        $this->feedbackForm->createFormRecord($this->formRecordId, $this->formRecordData);
    }
}

class TestableFeedbackForm extends FeedbackForm
{
    public $id;
    public $form;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
