<?php

namespace Participant\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Participant\Domain\DependencyModel\Firm\Program;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use Resources\DateTimeImmutableBuilder;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ProgramRegistrationTest extends TestBase
{
    protected $program;
    protected $programRegistration;
    protected $profile;

    protected $id = 'programRegistrationId';
    
    protected $programsProfileForm, $formRecordData;
    protected $registrantProfile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        
        $this->programRegistration = new TestableProgramRegistration($this->program, 'id');
        $this->programRegistration->profiles = new ArrayCollection();
        
        $this->profile = $this->buildMockOfClass(RegistrantProfile::class);
        $this->programRegistration->profiles->add($this->profile);
        
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->registrantProfile = $this->buildMockOfClass(RegistrantProfile::class);
    }
    
    protected function assertConcludedRegistrantForbidden(callable $operation): void
    {
        $errorDetail = "forbidden: only unconcluded registrant can make this request";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    public function test_construct_setProperties()
    {
        $programRegistration = new TestableProgramRegistration($this->program, $this->id);
        $this->assertEquals($this->program, $programRegistration->program);
        $this->assertEquals($this->id, $programRegistration->id);
        $this->assertFalse($programRegistration->concluded);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $programRegistration->registeredTime);
        $this->assertNull($programRegistration->note);
    }
    
    protected function executeCancel()
    {
        $this->programRegistration->cancel();
    }
    public function test_cancel_setConcludedTrueAndNoteCancelled()
    {
        $this->executeCancel();
        $this->assertTrue($this->programRegistration->concluded);
        $this->assertEquals('cancelled', $this->programRegistration->note);
    }
    public function test_cancel_alreadyConcluded_forbiddenError()
    {
        $this->programRegistration->concluded = true;
        
        $operation = function (){
            $this->executeCancel();
        };
        $errorDetail = 'forbidden: program registration already concluded';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeIsUnconcludedRegistrationToProgram()
    {
        return $this->programRegistration->isUnconcludedRegistrationToProgram($this->program);
    }
    public function test_isUnconcludedRegistrationToProgram_returnTrue()
    {
        $this->assertTrue($this->executeIsUnconcludedRegistrationToProgram());
    }
    public function test_isUnconcludedRegistrationToProgram_differentProgramId_returnFalse()
    {
        $program = $this->buildMockOfClass(Program::class);
        $this->assertFalse($this->programRegistration->isUnconcludedRegistrationToProgram($program));
    }
    public function test_isUnconcludedRegistrationToProgram_concludedProgramRegistration_returnFalse()
    {
        $this->programRegistration->concluded = true;
        $this->assertFalse($this->executeIsUnconcludedRegistrationToProgram());
    }
    
    protected function executeSubmitProfile()
    {
        $this->programsProfileForm->expects($this->any())->method("programEquals")->willReturn(true);
        $this->programRegistration->submitProfile($this->programsProfileForm, $this->formRecordData);
    }
    public function test_execute_addProfileToCollection()
    {
        $this->executeSubmitProfile();
        $this->assertEquals(2, $this->programRegistration->profiles->count());
        $this->assertInstanceOf(RegistrantProfile::class, $this->programRegistration->profiles->last());
    }
    public function test_executeSubmitProfile_programsProfileFormFromDifferentProgram_forbidden()
    {
        $this->programsProfileForm->expects($this->once())
                ->method("programEquals")
                ->with($this->program)
                ->willReturn(false);
        $operation = function (){
            $this->executeSubmitProfile();
        };
        $errorDetail = "forbidden: unable to submit profile from other program's profile template";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_executeSubmitProfile_anActiveProfileCorrespondToSameProgramsProfileFormAlreadyExist_updateExistingProfile()
    {
        $this->profile->expects($this->once())
                ->method("anActiveProfileCorrespondWithProgramsProfileForm")
                ->with($this->programsProfileForm)
                ->willReturn(true);
        
        $this->profile->expects($this->once())
                ->method("update")
                ->with($this->formRecordData);
        
        $this->executeSubmitProfile();
    }
    public function test_execute_updateOccured_preventAddNewProfile()
    {
        $this->profile->expects($this->once())
                ->method("anActiveProfileCorrespondWithProgramsProfileForm")
                ->willReturn(true);
        $this->executeSubmitProfile();
        $this->assertEquals(1, $this->programRegistration->profiles->count());
    }
    public function test_executeSubmitProfile_concludedRegistrant_forbidden()
    {
        $this->programRegistration->concluded = true;
        $this->assertConcludedRegistrantForbidden(function (){
            $this->executeSubmitProfile();
        });
    }
    
    protected function executeRemoveProfile()
    {
        $this->registrantProfile->expects($this->any())
                ->method("belongsToRegistrant")
                ->willReturn(true);
        $this->programRegistration->removeProfile($this->registrantProfile);
    }
    public function test_removeProfile_removeRegistrantProfile()
    {
        $this->registrantProfile->expects($this->once())
                ->method("remove");
        $this->executeRemoveProfile();
    }
    public function test_removeProfile_profileDoesntBelongToRegistrant_forbidden()
    {
        $this->registrantProfile->expects($this->once())
                ->method("belongsToRegistrant")
                ->with($this->programRegistration)
                ->willReturn(false);
        $operation = function (){
            $this->executeRemoveProfile();
        };
        $errorDetail = "forbidden: can only remove self profile";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    public function test_removeProfile_concludedRegistration_forbidden()
    {
        $this->programRegistration->concluded = true;
        $this->assertConcludedRegistrantForbidden(function (){
            $this->executeRemoveProfile();
        });
    }
}

class TestableProgramRegistration extends ProgramRegistration
{
    public $program;
    public $id;
    public $concluded;
    public $registeredTime;
    public $note;
    public $profiles;
}
