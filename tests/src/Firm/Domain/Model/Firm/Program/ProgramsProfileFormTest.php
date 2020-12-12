<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\ProfileForm;
use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;

class ProgramsProfileFormTest extends TestBase
{

    protected $program;
    protected $profileForm;
    protected $programsProfileForm;
    protected $id = "new Id";
    protected $firm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->profileForm = $this->buildMockOfClass(ProfileForm::class);

        $this->programsProfileForm = new TestableProgramsProfileForm($this->program, 'Id', $this->profileForm);
        
        $this->firm = $this->buildMockOfClass(Firm::class);
    }
    
    public function test_construct_setProperties()
    {
        $programsProfileForm = new TestableProgramsProfileForm($this->program, $this->id, $this->profileForm);
        $this->assertEquals($this->program, $programsProfileForm->program);
        $this->assertEquals($this->id, $programsProfileForm->id);
        $this->assertEquals($this->profileForm, $programsProfileForm->profileForm);
        $this->assertFalse($programsProfileForm->disabled);
    }
    
    protected function executeDisable()
    {
        $this->programsProfileForm->disable();
    }
    public function test_disable_setDisabledTrue()
    {
        $this->executeDisable();
        $this->assertTrue($this->programsProfileForm->disabled);
    }
    public function test_disable_alreadyDisabled_forbidden()
    {
        $this->programsProfileForm->disabled = true;
        $operation = function (){
            $this->executeDisable();
        };
        $errorDetail = "forbidden: program's profile form already disabled";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeEnable()
    {
        $this->programsProfileForm->enable();
    }
    public function test_enable_setDisabledFalse()
    {
        $this->programsProfileForm->disabled = true;
        $this->executeEnable();
        $this->assertFalse($this->programsProfileForm->disabled);
    }
    public function test_enalbe_alreadyEnabled_forbidden()
    {
        $operation = function (){
            $this->executeEnable();
        };
        $errorDetail = "forbidden: program's profile form already enabled";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_belongsToFirm_returnProgramsBelongsToFirmResult()
    {
        $this->program->expects($this->once())
                ->method("belongsToFirm")
                ->with($this->firm);
        $this->programsProfileForm->belongsToFirm($this->firm);
    }
    
    public function test_correspondWithProfileForm_sameProfileForm_returnTrue()
    {
        $this->assertTrue($this->programsProfileForm->correspondWithProfileForm($this->profileForm));
    }
    public function test_correspondWithProfileForm_differentProfileForm_returnFalse()
    {
        $profileForm = $this->buildMockOfClass(ProfileForm::class);
        $this->assertFalse($this->programsProfileForm->correspondWithProfileForm($profileForm));
    }
}

class TestableProgramsProfileForm extends ProgramsProfileForm
{
    public $program;
    public $id;
    public $profileForm;
    public $disabled;
}
