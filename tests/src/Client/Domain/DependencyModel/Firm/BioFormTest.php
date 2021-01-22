<?php

namespace Client\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class BioFormTest extends TestBase
{
    protected $bioForm;
    protected $form;
    protected $formRecordId = "formRecordId", $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->bioForm = new TestableBioForm();
        $this->form = $this->buildMockOfClass(\SharedContext\Domain\Model\SharedEntity\Form::class);
        $this->bioForm->form = $this->form;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeCreateFormRecord()
    {
        return $this->bioForm->createFormRecord($this->formRecordId, $this->formRecordData);
    }
    public function test_createFormRecord_returnFormRecordCreatedInForm()
    {
        $this->form->expects($this->once())
                ->method("createFormRecord")
                ->with($this->formRecordId, $this->formRecordData);
        $this->executeCreateFormRecord();
    }
    public function test_createFormRecord_disabledForm_forbidden()
    {
        $this->bioForm->disabled = true;
        $operation = function (){
            $this->executeCreateFormRecord();
        };
        $errorDetail = "forbidden: can only submit CV on active Form";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_belongsToFirm_sameFirmId_returnTrue()
    {
        $this->assertTrue($this->bioForm->belongsToFirm($this->bioForm->firmId));
    }
    public function test_belongsToFirm_differentFirmId_returnFalse()
    {
        $this->assertFalse($this->bioForm->belongsToFirm("differentFirmId"));
    }
}

class TestableBioForm extends BioForm
{
    public $firmId = "firmId";
    public $form;
    public $disabled = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
