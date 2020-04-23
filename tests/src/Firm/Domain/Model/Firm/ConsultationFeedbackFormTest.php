<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\ {
    Firm,
    Shared\Form,
    Shared\FormData
};
use Tests\TestBase;

class ConsultationFeedbackFormTest extends TestBase
{
    protected $firm;
    protected $form;
    protected $consultationFeedbackForm;
    
    protected $id = 'consultation-feedback-form-id';
    protected $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->form = $this->buildMockOfClass(Form::class);
        
        $this->consultationFeedbackForm = new TestableConsultationFeedbackForm($this->firm, 'id', $this->form);
        
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    public function test_construct_setProperties()
    {
        $consultationFeedbackForm = new TestableConsultationFeedbackForm($this->firm, $this->id, $this->form);
        $this->assertEquals($this->firm, $consultationFeedbackForm->firm);
        $this->assertEquals($this->id, $consultationFeedbackForm->id);
        $this->assertEquals($this->form, $consultationFeedbackForm->form);
    }
    
    public function test_update_updateForm()
    {
        $this->form->expects($this->once())
            ->method('update')
            ->with($this->formData);
        $this->consultationFeedbackForm->update($this->formData);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->consultationFeedbackForm->remove();
        $this->assertTrue($this->consultationFeedbackForm->removed);
    }
    
}

class TestableConsultationFeedbackForm extends ConsultationFeedbackForm
{
    public $firm, $id, $form, $removed;
}
