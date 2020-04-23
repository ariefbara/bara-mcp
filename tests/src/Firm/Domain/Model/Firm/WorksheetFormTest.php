<?php

namespace Firm\Domain\Model\Firm;

use Firm\Domain\Model\ {
    Firm,
    Shared\Form,
    Shared\FormData
};
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
}

class TestableWorksheetForm extends WorksheetForm{
    public $firm, $id, $form, $removed;
}
