<?php

namespace Participant\Domain\Model\Registrant;

use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\ProgramRegistration;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class RegistrantProfileTest extends TestBase
{
    protected $registrant;
    protected $programsProfileForm;
    protected $formRecordData;
    protected $registrantProfile;
    protected $formRecord;
    protected $id = "newId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrant = $this->buildMockOfClass(ProgramRegistration::class);
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->registrantProfile = new TestableRegistrantProfile(
                $this->registrant, "id", $this->programsProfileForm, $this->formRecordData);
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->registrantProfile->formRecord = $this->formRecord;
    }
    
    protected function assertAlreadyRemovedForbidden(callable $operation): void
    {
        $errorDetail = "forbidden: unable not process non existing profile";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeConstruct()
    {
        return new TestableRegistrantProfile($this->registrant, $this->id, $this->programsProfileForm, $this->formRecordData);
        
    }
    public function test_construct_setProperties()
    {
        $this->programsProfileForm->expects($this->once())
                ->method("createFormRecord")
                ->with($this->id, $this->formRecordData)
                ->willReturn($this->formRecord);
        
        $registrantProfile = $this->executeConstruct();
        $this->assertEquals($this->registrant, $registrantProfile->registrant);
        $this->assertEquals($this->id, $registrantProfile->id);
        $this->assertEquals($this->programsProfileForm, $registrantProfile->programsProfileForm);
        $this->assertEquals($this->formRecord, $registrantProfile->formRecord);
        $this->assertFalse($registrantProfile->removed);
    }
    
    protected function executeUpdate()
    {
        $this->registrantProfile->update($this->formRecordData);
    }
    public function test_update_updateFormRecord()
    {
        $this->formRecord->expects($this->once())
                ->method("update")
                ->with($this->formRecordData);
        $this->executeUpdate();
    }
    public function test_update_alreadyRemoved_forbidden()
    {
        $this->registrantProfile->removed = true;
        $this->assertAlreadyRemovedForbidden(function (){
            $this->executeUpdate();
        });
    }
    
    protected function executeRemove()
    {
        $this->registrantProfile->remove();
    }
    public function test_remove_setRemovedTrue()
    {
        $this->executeRemove();
        $this->assertTrue($this->registrantProfile->removed);
    }
    public function test_remove_alreadyRemoved_forbidden()
    {
        $this->registrantProfile->removed = true;
        $this->assertAlreadyRemovedForbidden(function (){
            $this->executeRemove();
        });
    }
    
    protected function executeAnActiveProfileCorrespondWithProgramsProfileForm()
    {
        return $this->registrantProfile->anActiveProfileCorrespondWithProgramsProfileForm($this->programsProfileForm);
    }
    public function test_anActiveProfileCorrespondWithProgramsProfileForm_sameProgramsProfileForm_returnTrue()
    {
        $this->assertTrue($this->executeAnActiveProfileCorrespondWithProgramsProfileForm());
    }
    public function test_anActiveProfileCorrespondWithProgramsProfileForm_differentProgramsProfileForm_returnFalse()
    {
        $this->registrantProfile->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->assertFalse($this->executeAnActiveProfileCorrespondWithProgramsProfileForm());
    }
    public function test_anActiveProfileCorrespondWithProgramsProfileForm_alreadyRemoved_returnFalse()
    {
        $this->registrantProfile->removed = true;
        $this->assertFalse($this->executeAnActiveProfileCorrespondWithProgramsProfileForm());
    }
    
    public function test_belongsToRegistrant_sameRegistrant_returnTrue()
    {
        $this->assertTrue($this->registrantProfile->belongsToRegistrant($this->registrant));
    }
    public function test_belongsToRegistrant_differentRegistrant_returnFalse()
    {
        $registrant = $this->buildMockOfClass(ProgramRegistration::class);
        $this->assertFalse($this->registrantProfile->belongsToRegistrant($registrant));
    }
}

class TestableRegistrantProfile extends RegistrantProfile
{
    public $registrant;
    public $id;
    public $programsProfileForm;
    public $formRecord;
    public $removed;
}
