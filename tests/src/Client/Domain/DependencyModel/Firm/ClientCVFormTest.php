<?php

namespace Client\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ClientCVFormTest extends TestBase
{
    protected $clientCVForm;
    protected $profileForm;
    protected $formRecordId = "formRecordId", $formRecordData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->clientCVForm = new TestableClientCVForm();
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);
        $this->clientCVForm->profileForm = $this->profileForm;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function executeCreateFormRecord()
    {
        return $this->clientCVForm->createFormRecord($this->formRecordId, $this->formRecordData);
    }
    public function test_createFormRecord_returnFormRecordCreatedInProfileForm()
    {
        $this->profileForm->expects($this->once())
                ->method("createFormRecord")
                ->with($this->formRecordId, $this->formRecordData);
        $this->executeCreateFormRecord();
    }
    public function test_createFormRecord_disabledForm_forbidden()
    {
        $this->clientCVForm->disabled = true;
        $operation = function (){
            $this->executeCreateFormRecord();
        };
        $errorDetail = "forbidden: can only submit CV on active Form";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_belongsToFirm_sameFirmId_returnTrue()
    {
        $this->assertTrue($this->clientCVForm->belongsToFirm($this->clientCVForm->firmId));
    }
    public function test_belongsToFirm_differentFirmId_returnFalse()
    {
        $this->assertFalse($this->clientCVForm->belongsToFirm("differentFirmId"));
    }
}

class TestableClientCVForm extends ClientCVForm
{
    public $firmId = "firmId";
    public $id = "id";
    public $profileForm;
    public $disabled = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
