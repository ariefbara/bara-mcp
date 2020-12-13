<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

use Firm\Domain\Model\Firm\Program\Participant;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use Tests\TestBase;

class ParticipantProfileTest extends TestBase
{
    protected $participantProfile;
    protected $participant;
    protected $programsProfileForm;
    protected $formRecord;
    protected $id = "newId";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
    }
    
    public function test_construct_setProperties()
    {
        $participantProfile = new TestableParticipantProfile(
                $this->participant, $this->id, $this->programsProfileForm, $this->formRecord);
        $this->assertEquals($this->participant, $participantProfile->participant);
        $this->assertEquals($this->id, $participantProfile->id);
        $this->assertEquals($this->programsProfileForm, $participantProfile->programsProfileForm);
        $this->assertEquals($this->formRecord, $participantProfile->formRecord);
        $this->assertFalse($participantProfile->removed);
    }
}

class TestableParticipantProfile extends ParticipantProfile
{
    public $participant;
    public $id;
    public $programsProfileForm;
    public $formRecord;
    public $removed;
}
