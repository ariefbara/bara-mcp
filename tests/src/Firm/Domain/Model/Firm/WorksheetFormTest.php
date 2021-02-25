<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Shared\Form;
use Firm\Domain\Model\Shared\FormData;
use Tests\TestBase;

class WorksheetFormTest extends TestBase
{
    protected $firm;
    protected $form;
    protected $worksheetForm;
    protected $id = 'worksheet-form-id';
    protected $formData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->form = $this->buildMockOfClass(Form::class);
        $this->worksheetForm = new TestableWorksheetForm($this->firm, 'id', $this->form);
        
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())
                ->method('getName')
                ->willReturn('new form name');
    }
    public function test_construct_setProperties()
    {
        $worksheetForm = new TestableWorksheetForm($this->firm, $this->id, $this->form);
        $this->assertEquals($this->firm, $worksheetForm->firm);
        $this->assertEquals($this->id, $worksheetForm->id);
        $this->assertEquals($this->form, $worksheetForm->form);
        $this->assertFalse($worksheetForm->removed);
    }
    public function test_update_updateForm()
    {
        $this->form->expects($this->once())
            ->method('update')
            ->with($this->formData);
        $this->worksheetForm->update($this->formData);
    }
    public function test_remove_setRemovedFlagTrue()
    {
        $this->worksheetForm->remove();
        $this->assertTrue($this->worksheetForm->removed);
    }
    
    public function test_belongsToFirm_sameFirm_returnTrue()
    {
        $this->assertTrue($this->worksheetForm->belongsToFirm($this->worksheetForm->firm));
    }
    public function test_belongsToFirm_differentFirm_returnFalse()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->worksheetForm->belongsToFirm($firm));
    }
    
    public function test_isManageableByFirm_sameFirm_returnTrue()
    {
        $this->assertTrue($this->worksheetForm->isManageableByFirm($this->firm));
    }
    public function test_isManageableByFirm_differentFirm_returnFalse()
    {
        $firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->worksheetForm->isManageableByFirm($firm));
    }
    public function test_isManageableByFirm_emptyFirm_returnTrue()
    {
        $this->worksheetForm->firm = null;
        $this->assertTrue($this->worksheetForm->isManageableByFirm($this->firm));
    }
}

class TestableWorksheetForm extends WorksheetForm{
    public $firm, $id, $form, $removed;
}
