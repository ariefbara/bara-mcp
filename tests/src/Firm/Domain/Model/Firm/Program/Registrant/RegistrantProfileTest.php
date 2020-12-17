<?php

namespace Firm\Domain\Model\Firm\Program\Registrant;

use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use Tests\TestBase;

class RegistrantProfileTest extends TestBase
{
    protected $registrantProfile;
    protected $programsProfileForm;
    protected $formRecord;
    protected $participant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registrantProfile = new TestableRegistrantProfile();
        
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->registrantProfile->programsProfileForm = $this->programsProfileForm;
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->registrantProfile->formRecord = $this->formRecord;
        
        $this->participant = $this->buildMockOfClass(Participant::class);
    }
    
    public function test_transferToParticipant_addParticipantsProfile()
    {
        $this->participant->expects($this->once())
                ->method("addProfile")
                ->with($this->programsProfileForm, $this->formRecord);
        $this->registrantProfile->transferToParticipant($this->participant);
    }
}

class TestableRegistrantProfile extends RegistrantProfile
{
    public $registrant;
    public $id;
    public $programsProfileForm;
    public $formRecord;
    public $removed;
    
    function __construct()
    {
        parent::__construct();
    }
}
