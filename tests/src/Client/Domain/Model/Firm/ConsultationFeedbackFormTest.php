<?php

namespace Client\Domain\Model\Firm;

use Shared\Domain\Model\ {
    Form,
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class ConsultationFeedbackFormTest extends TestBase
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

class TestableConsultationFeedbackForm extends ConsultationFeedbackForm
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
