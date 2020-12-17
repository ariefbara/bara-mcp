<?php

namespace Participant\Domain\DependencyModel\Firm\Program;

use Participant\Domain\DependencyModel\Firm\ProfileForm;
use Participant\Domain\DependencyModel\Firm\Program;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ProgramsProfileFormTest extends TestBase
{
    protected $programsProfileForm;
    protected $program;
    protected $profileForm;
    protected $formRecordId = "formRecordId", $formRecordData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programsProfileForm = new TestableProgramsProfileForm();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programsProfileForm->program = $this->program;
        
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);
        $this->programsProfileForm->profileForm = $this->profileForm;
        
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    public function test_programEquals_sameProgram_returnTrue()
    {
        $this->assertTrue($this->programsProfileForm->programEquals($this->program));
    }
    public function test_programEquals_differenetProgram_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->programsProfileForm->programEquals($program));
    }
    
    protected function executeCreateFormRecord()
    {
        return $this->programsProfileForm->createFormRecord($this->formRecordId, $this->formRecordData);
    }
    public function test_createFormRecord_returnFormRecordCreatedInProfileForm()
    {
        $this->profileForm->expects($this->once())
                ->method("createFormRecord")
                ->with($this->formRecordId, $this->formRecordData);
        $this->executeCreateFormRecord();
    }
    public function test_createFormRecord_disabled_forbidden()
    {
        $this->programsProfileForm->disabled = true;
        $operation = function (){
            $this->executeCreateFormRecord();
        };
        $errorDetail = "forbidden: can only submit profile from enabled template";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
}

class TestableProgramsProfileForm extends ProgramsProfileForm
{
    public $program;
    public $id;
    public $profileForm;
    public $disabled = false;
    
    function __construct()
    {
        parent::__construct();
    }
}
