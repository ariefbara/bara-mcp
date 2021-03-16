<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Shared\FormData;
use Tests\TestBase;

class BioFormTest extends TestBase
{
    protected $firm;
    protected $bioForm;
    protected $form;
    protected $id = "newId", $formData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())->method("getName")->willReturn("bio form name");
        
        $this->bioForm = new TestableBioForm($this->firm, "id", $this->formData);
        
        $this->form = $this->buildMockOfClass(\Firm\Domain\Model\Shared\Form::class);
        $this->bioForm->form = $this->form;
    }
    
    protected function executeConstruct()
    {
        return new TestableBioForm($this->firm, $this->id, $this->formData);
    }
    
    public function test_construct_setProperties()
    {
        $bioForm = $this->executeConstruct();
        $this->assertEquals($this->firm, $bioForm->firm);
        
        $form = new \Firm\Domain\Model\Shared\Form($this->id, $this->formData);
        $this->assertEquals($form, $bioForm->form);
        $this->assertFalse($bioForm->disabled);
    }
    
    protected function executeUpdate()
    {
        $this->bioForm->update($this->formData);
    }
    public function test_update_updateForm()
    {
        $this->form->expects($this->once())
                ->method("update")
                ->with($this->formData);
        $this->executeUpdate();
    }
    public function test_update_disabled_forbidden()
    {
        $this->bioForm->disabled = true;
        $operation = function (){
            $this->executeUpdate();
        };
        $errorDetail = "forbidden: this request only valid on enabled bio form";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_disable_setDisabledTrue()
    {
        $this->bioForm->disable();
        $this->assertTrue($this->bioForm->disabled);
    }
    public function test_disable_alreadyDisabled_forbidden()
    {
        $this->bioForm->disabled = true;
        $operation = function (){
            $this->bioForm->disable();
        };
        $errorDetail = "forbidden: this request only valid on enabled bio form";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_enable_setDisabledFalse()
    {
        $this->bioForm->disabled = true;
        $this->bioForm->enable();
        $this->assertFalse($this->bioForm->disabled);
    }
    public function test_enable_alreadyEnabled_forbidden()
    {
        $operation = function (){
            $this->bioForm->enable();
        };
        $errorDetail = "forbidden: bio form already enabled";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_belongsToFirm_sameFirm_returnTrue()
    {
        $this->assertTrue($this->bioForm->belongsToFirm($this->bioForm->firm));
    }
    public function test_belongsToFirm_differentFirm_returnFalse()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->bioForm->belongsToFirm($firm));
    }
    
}

class TestableBioForm extends BioForm
{
    public $firm;
    public $id;
    public $form;
    public $disabled;
}
