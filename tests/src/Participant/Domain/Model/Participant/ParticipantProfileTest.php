<?php

namespace Participant\Domain\Model\Participant;

use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Participant;
use SharedContext\Domain\Model\SharedEntity\FormRecord;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class ParticipantProfileTest extends TestBase
{

    protected $participant;
    protected $programsProfileForm;
    protected $formRecordData;
    protected $participantProfile;
    protected $formRecord;
    protected $id = "newId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
        $this->participantProfile = new TestableParticipantProfile(
                $this->participant, 'id', $this->programsProfileForm, $this->formRecordData);
        
        $this->formRecord = $this->buildMockOfClass(FormRecord::class);
        $this->participantProfile->formRecord = $this->formRecord;
    }
    protected function assertAlreadyRemovedForbidden(callable $operation): void
    {
        $errorDetail = "forbidden: unable not process non existing profile";
        $this->assertRegularExceptionThrowed($operation, "Forbidden", $errorDetail);
    }
    
    protected function executeConstruct()
    {
        return new TestableParticipantProfile(
                $this->participant, $this->id, $this->programsProfileForm, $this->formRecordData);
    }
    public function test_construct_setProperties()
    {
        $this->programsProfileForm->expects($this->once())
                ->method("createFormRecord")
                ->with($this->id, $this->formRecordData)
                ->willReturn($this->formRecord);
        
        $participantProfile = $this->executeConstruct();
        $this->assertEquals($this->participant, $participantProfile->participant);
        $this->assertEquals($this->id, $participantProfile->id);
        $this->assertEquals($this->programsProfileForm, $participantProfile->programsProfileForm);
        $this->assertEquals($this->formRecord, $participantProfile->formRecord);
        $this->assertFalse($participantProfile->removed);
    }
    
    protected function executeUpdate()
    {
        $this->participantProfile->update($this->formRecordData);
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
        $this->participantProfile->removed = true;
        $this->assertAlreadyRemovedForbidden(function (){
            $this->executeUpdate();
        });
    }
    
    protected function executeRemove()
    {
        $this->participantProfile->remove();
    }
    public function test_remove_setRemovedTrue()
    {
        $this->executeRemove();
        $this->assertTrue($this->participantProfile->removed);
    }
    public function test_remove_alreadyRemoved_forbidden()
    {
        $this->participantProfile->removed = true;
        $this->assertAlreadyRemovedForbidden(function (){
            $this->executeRemove();
        });
    }
    
    protected function executeAnActiveProfileCorrespondWithProgramsProfileForm()
    {
        return $this->participantProfile->anActiveProfileCorrespondWithProgramsProfileForm($this->programsProfileForm);
    }
    public function test_anActiveProfileCorrespondWithProgramsProfileForm_sameProgramsProfileForm_returnTrue()
    {
        $this->assertTrue($this->executeAnActiveProfileCorrespondWithProgramsProfileForm());
    }
    public function test_anActiveProfileCorrespondWithProgramsProfileForm_differentProgramsProfileForm_returnFalse()
    {
        $this->participantProfile->programsProfileForm = $this->buildMockOfClass(ProgramsProfileForm::class);
        $this->assertFalse($this->executeAnActiveProfileCorrespondWithProgramsProfileForm());
    }
    public function test_anActiveProfileCorrespondWithProgramsProfileForm_alreadyRemoved_returnFalse()
    {
        $this->participantProfile->removed = true;
        $this->assertFalse($this->executeAnActiveProfileCorrespondWithProgramsProfileForm());
    }
    
    public function test_belongsToParticipant_sameParticipant_returnTrue()
    {
        $this->assertTrue($this->participantProfile->belongsToParticipant($this->participant));
    }
    public function test_belongsToParticipant_differentParticipant_returnFalse()
    {
        $participant = $this->buildMockOfClass(Participant::class);
        $this->assertFalse($this->participantProfile->belongsToParticipant($participant));
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
