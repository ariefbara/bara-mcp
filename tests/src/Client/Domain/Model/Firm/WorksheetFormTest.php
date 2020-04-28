<?php

namespace Client\Domain\Model\Firm;

use Shared\Domain\Model\ {
    Form,
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class WorksheetFormTest extends TestBase
{
    protected $form;
    protected $worksheetForm;
    protected $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetForm = new TestableWorksheetForm();
        $this->form = $this->buildMockOfClass(Form::class);
        $this->worksheetForm->form = $this->form;
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_createFormRecord_returnFormRecord()
    {
        $this->assertInstanceOf(FormRecord::class, $this->worksheetForm->createFormRecord('id', $this->formRecordData));
    }
}

class TestableWorksheetForm extends WorksheetForm
{
    public $firm;
    public $id;
    public $form;
    public $removed = false;
    
    public function __construct()
    {
        ;
    }
}
