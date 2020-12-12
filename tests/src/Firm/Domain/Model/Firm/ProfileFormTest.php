<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Shared\Form;
use Firm\Domain\Model\Shared\FormData;
use Tests\TestBase;

class ProfileFormTest extends TestBase
{
    protected $firm;
    protected $formData;
    protected $profileForm;
    protected $form;
    protected $id = "newId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())->method("getName")->willReturn("formName");
        
        $this->profileForm = new TestableProfileForm($this->firm, 'id', $this->formData);
        
        $this->form = $this->buildMockOfClass(Form::class);
        $this->profileForm->form = $this->form;
    }
    
    public function test_construct_setProperties()
    {
        $profileForm = new TestableProfileForm($this->firm, $this->id, $this->formData);
        $this->assertEquals($this->firm, $profileForm->firm);
        $this->assertEquals($this->id, $profileForm->id);
        
        $form = new Form($this->id, $this->formData);
        $this->assertEquals($form, $profileForm->form);
        
    }
    
    public function test_update_updateForm()
    {
        $this->form->expects($this->once())
                ->method("update")
                ->with($this->formData);
        $this->profileForm->update($this->formData);
    }
    
    public function test_belongsToFirm_sameFirm_returnTrue()
    {
        $this->assertTrue($this->profileForm->belongsToFirm($this->profileForm->firm));
    }
    public function test_belongsToFirm_differentFirm_returnFalse()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->profileForm->belongsToFirm($firm));
    }
}

class TestableProfileForm extends ProfileForm
{
    public $firm;
    public $id;
    public $form;
}
