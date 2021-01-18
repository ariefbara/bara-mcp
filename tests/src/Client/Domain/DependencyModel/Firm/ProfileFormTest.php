<?php

namespace Client\Domain\DependencyModel\Firm;

use SharedContext\Domain\Model\SharedEntity\Form;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ProfileFormTest extends TestBase
{
    protected $profileForm;
    protected $form;
    protected $formRecordId = "formRecordId",$formRecordData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->profileForm = new TestableProfileForm();
        $this->form = $this->buildMockOfClass(Form::class);
        $this->profileForm->form = $this->form;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_createFormRecord_returnFormRecordCreatedInForm()
    {
        $this->form->expects($this->once())
                ->method("createFormRecord")
                ->with($this->formRecordId, $this->formRecordData);
        $this->profileForm->createFormRecord($this->formRecordId, $this->formRecordData);
    }
}

class TestableProfileForm extends ProfileForm
{
    public $firmId = "firmId";
    public $id = "id";
    public $form;
    
    function __construct()
    {
        parent::__construct();
    }
}
