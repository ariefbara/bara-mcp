<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\ {
    Firm,
    Shared\Form,
    Shared\FormData
};
use Tests\TestBase;

class FeedbackFormTest extends TestBase
{
    protected $firm;
    protected $form;
    protected $feedbackForm;
    
    protected $id = 'consultation-feedback-form-id';
    protected $formData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->form = $this->buildMockOfClass(Form::class);
        
        $this->feedbackForm = new TestableFeedbackForm($this->firm, 'id', $this->form);
        
        $this->formData = $this->buildMockOfClass(FormData::class);
    }
    
    public function test_construct_setProperties()
    {
        $feedbackForm = new TestableFeedbackForm($this->firm, $this->id, $this->form);
        $this->assertEquals($this->firm, $feedbackForm->firm);
        $this->assertEquals($this->id, $feedbackForm->id);
        $this->assertEquals($this->form, $feedbackForm->form);
    }
    
    public function test_update_updateForm()
    {
        $this->form->expects($this->once())
            ->method('update')
            ->with($this->formData);
        $this->feedbackForm->update($this->formData);
    }
    
    public function test_remove_setRemovedFlagTrue()
    {
        $this->feedbackForm->remove();
        $this->assertTrue($this->feedbackForm->removed);
    }
    
}

class TestableFeedbackForm extends FeedbackForm
{
    public $firm, $id, $form, $removed;
}
