<?php

namespace SharedContext\Domain\Model\Firm;

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
    protected $id = 'newId', $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationFeedbackForm = new TestableConsultationFeedbackForm();
        $this->form = $this->buildMockOfClass(Form::class);
        $this->consultationFeedbackForm->form = $this->form;
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    public function test_createFormRecord_returnFormRecord()
    {
        $this->assertInstanceOf(FormRecord::class, $this->consultationFeedbackForm->createFormRecord($this->id, $this->formRecordData));
    }
}

class TestableConsultationFeedbackForm extends FeedbackForm
{
    public $firm;
    public $id;
    public $form;
    public $removed;
    
    public function __construct()
    {
        parent::__construct();
    }
}
