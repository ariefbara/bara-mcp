<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class DeclaredMentoringStatusTest extends TestBase
{
    protected $declaredStatus;
    protected $statusValue;
    protected $declaredStatusList;

    protected function setUp(): void
    {
        parent::setUp();
        $this->declaredStatus = new TestableDeclaredMentoringStatus(DeclaredMentoringStatus::DECLARED_BY_MENTOR);
        $this->statusValue = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $this->declaredStatusList = [
            DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            DeclaredMentoringStatus::APPROVED_BY_MENTOR,
        ];
    }
    
    protected function statusEquals()
    {
        return $this->declaredStatus->statusEquals($this->statusValue);
    }
    public function test_statusEquals_sameValue_returnTrue()
    {
        $this->assertTrue($this->statusEquals());
    }
    public function test_statusEquals_differentValue_returnFalse()
    {
        $this->declaredStatus->value = DeclaredMentoringStatus::APPROVED_BY_MENTOR;
        $this->assertFalse($this->statusEquals());
    }
    
    protected function cancelMentorDeclaration()
    {
        return $this->declaredStatus->cancelMentorDeclaration();
    }
    public function test_cancelMentorDeclaration_returnCancelledStatus()
    {
        $cancelledStatus = new TestableDeclaredMentoringStatus(DeclaredMentoringStatus::CANCELLED);
        $this->assertEquals($cancelledStatus, $this->cancelMentorDeclaration());
    }
    public function test_cancelMentorDeclaration_notDeclaredByMentor_forbidden()
    {
        $this->declaredStatus->value = DeclaredMentoringStatus::APPROVED_BY_MENTOR;
        $this->assertRegularExceptionThrowed(function() {
            $this->cancelMentorDeclaration();
        }, 'Forbidden', 'forbidden: can only cancel declaration in declared by mentor state');
    }
    
    protected function approveParticipantDeclaration()
    {
        return $this->declaredStatus->approveParticipantDeclaration();
    }
    public function test_approveParticipantDeclaration_returnApprovedStatus()
    {
        $this->declaredStatus->value = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $approvedStatus = new TestableDeclaredMentoringStatus(DeclaredMentoringStatus::APPROVED_BY_MENTOR);
        $this->assertEquals($approvedStatus, $this->approveParticipantDeclaration());
    }
    public function test_approveParticipantDeclaration_notDeclaredByParticipant_forbidden()
    {
        $this->declaredStatus->value = DeclaredMentoringStatus::APPROVED_BY_MENTOR;
        $this->assertRegularExceptionThrowed(function() {
            $this->approveParticipantDeclaration();
        }, 'Forbidden', 'forbidden: can only approve declaration in declared by participant state');
    }
    
    protected function denyParticipantDeclaration()
    {
        return $this->declaredStatus->denyParticipantDeclaration();
    }
    public function test_denyParticipantDeclaration_returnDeniedStatus()
    {
        $this->declaredStatus->value = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $deniedStatus = new TestableDeclaredMentoringStatus(DeclaredMentoringStatus::DENIED_BY_MENTOR);
        $this->assertEquals($deniedStatus, $this->denyParticipantDeclaration());
    }
    public function test_denyParticipantDeclaration_notDeclaredByParticipant_forbidden()
    {
        $this->declaredStatus->value = DeclaredMentoringStatus::APPROVED_BY_MENTOR;
        $this->assertRegularExceptionThrowed(function() {
            $this->denyParticipantDeclaration();
        }, 'Forbidden', 'forbidden: can only deny declaration in declared by participant state');
    }
    
    protected function statusIn()
    {
        return $this->declaredStatus->statusIn($this->declaredStatusList);
    }
    public function test_statusIn_statusValueContainInList_returnTrue()
    {
        $this->assertTrue($this->statusIn());
    }
    public function test_statusIn_statusValueNotInList_returnFalse()
    {
        $this->declaredStatus->value = DeclaredMentoringStatus::CANCELLED;
        $this->assertFalse($this->statusIn());
    }
}

class TestableDeclaredMentoringStatus extends DeclaredMentoringStatus
{
    public $value;
}
