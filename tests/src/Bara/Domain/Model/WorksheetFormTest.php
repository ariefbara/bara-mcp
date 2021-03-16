<?php

namespace Bara\Domain\Model;

use Firm\Domain\Model\Shared\Form;
use Firm\Domain\Model\Shared\FormData;
use Tests\TestBase;

class WorksheetFormTest extends TestBase
{
    protected $formData;
    protected $form;
    protected $worksheetForm;
    protected $id = 'newId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->formData = $this->buildMockOfClass(FormData::class);
        $this->formData->expects($this->any())->method('getName')->willReturn('form name');
        $this->worksheetForm = new TestableWorksheetForm('id', $this->formData);
        $this->form = $this->buildMockOfClass(Form::class);
        $this->worksheetForm->form = $this->form;
    }
    
    public function test_construct_setProperties()
    {
        $worksheetForm = new TestableWorksheetForm($this->id, $this->formData);
        $this->assertNull($worksheetForm->firmId);
        $this->assertEquals($this->id, $worksheetForm->id);
        $form = new Form($this->id, $this->formData);
        $this->assertEquals($form, $worksheetForm->form);
        $this->assertFalse($worksheetForm->removed);
    }
    
    public function test_update_updateForm()
    {
        $this->form->expects($this->once())
                ->method('update')
                ->with($this->formData);
        $this->worksheetForm->update($this->formData);
    }
    
    public function test_remove_setRemovedTrue()
    {
        $this->worksheetForm->remove();
        $this->assertTrue($this->worksheetForm->removed);
    }
    
    public function test_isGlobalAsset_nullFirmId_returnTrue()
    {
        $this->assertTrue($this->worksheetForm->isGlobalAsset());
    }
    public function test_isGlobalAsset_notNullFirmId_returnFalse()
    {
        $this->worksheetForm->firmId = 'firmId';
        $this->assertFalse($this->worksheetForm->isGlobalAsset());
    }
}

class TestableWorksheetForm extends WorksheetForm
{
    public $firmId = null;
    public $id;
    public $form;
    public $removed;
}
