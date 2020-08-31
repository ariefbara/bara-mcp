<?php

namespace Participant\Domain\Model\DependencyEntity\Firm;

use SharedContext\Domain\Model\SharedEntity\ {
    Form,
    FormRecord,
    FormRecordData
};
use Tests\TestBase;

class WorksheetFormTest extends TestBase
{

    protected $form;
    protected $worksheetForm;
    
    protected $formRecordId = 'formRecordId', $formRecordData;

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
        $formRecord = new FormRecord($this->form, $this->formRecordId, $this->formRecordData);
        $this->assertEquals($formRecord, $this->worksheetForm->createFormRecord($this->formRecordId, $this->formRecordData));
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
