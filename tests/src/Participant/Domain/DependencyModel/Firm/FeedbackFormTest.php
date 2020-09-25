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


    protected function setUp(): void
    {
        parent::setUp();
        $this->feedbackForm = new TestableFeedbackForm();
        
        $this->form = $this->buildMockOfClass(Form::class);
        $this->feedbackForm->form = $this->form;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    public function test_createFormRecord_returnFormRecord()
    {
        $formRecord = new FormRecord($this->form, $this->formRecordId, $this->formRecordData);
        $this->assertEquals($formRecord, $this->feedbackForm->createFormRecord($this->formRecordId, $this->formRecordData));
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
