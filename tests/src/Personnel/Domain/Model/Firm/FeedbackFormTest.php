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


    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationFeedbackForm = new TestableFeedbackForm();
        $this->form = $this->buildMockOfClass(Form::class);
        $this->consultationFeedbackForm->form = $this->form;
    }
    
    public function test_createFormRecord_returnFormRecord()
    {
        $formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->assertInstanceOf(FormRecord::class, $this->consultationFeedbackForm->createFormRecord($id = 'id', $formRecordData));
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
