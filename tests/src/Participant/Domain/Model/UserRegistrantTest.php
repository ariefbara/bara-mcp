<?php

namespace Participant\Domain\Model;

use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Registrant\RegistrantProfile;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class UserRegistrantTest extends TestBase
{
    protected $userRegistrant;
    protected $registrant;
    protected $programsProfileForm, $formRecordData;
    protected $registrantProfile;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userRegistrant = new TestableUserRegistrant();
        $this->registrant = $this->buildMockOfClass(ProgramRegistration::class);
        $this->userRegistrant->registrant = $this->registrant;
        
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        
        $this->registrantProfile = $this->buildMockOfClass(RegistrantProfile::class);
    }
    
    public function test_submitProfile_submitProfileInRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method("submitProfile")
                ->with($this->programsProfileForm, $this->formRecordData);
        $this->userRegistrant->submitProfile($this->programsProfileForm, $this->formRecordData);
    }
    
    public function test_removeProfile_removeProfileInRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method("removeProfile")
                ->with($this->registrantProfile);
        $this->userRegistrant->removeProfile($this->registrantProfile);
    }
}

class TestableUserRegistrant extends UserRegistrant
{
    public $userId;
    public $id;
    public $registrant;
    
    function __construct()
    {
        parent::__construct();
    }
}
