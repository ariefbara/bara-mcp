<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program\Participant\DedicatedMentor;
use Tests\src\Firm\Domain\Model\Firm\Program\CoordinatorTestBase;

class Coordinator_ManageDedicatedMentorTest extends CoordinatorTestBase
{
    protected $participant;
    protected $consultant;
    protected $dedicatedMentor;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->consultant = $this->buildMockOfClass(Consultant::class);
        $this->dedicatedMentor = $this->buildMockOfClass(DedicatedMentor::class);
    }
    
    protected function executeDedicateMentorToParticipant()
    {
        $this->setAssetManageable($this->participant);
        $this->setAssetManageable($this->consultant);
        return $this->coordinator->dedicateMentorToParticipant($this->participant, $this->consultant);
    }
    public function test_dedicateMentorToParticipant_dedicateMentorInParticipant()
    {
        $this->participant->expects($this->once())
                ->method('dedicateMentor')
                ->with($this->consultant);
        $this->executeDedicateMentorToParticipant();
    }
    public function test_dedicateMentorToParticipant_returnDedicatedMentorIdResul()
    {
        $this->participant->expects($this->once())
                ->method('dedicateMentor')
                ->willReturn($dedicatedMentorId = 'dedicatedMentorId');
        $this->assertEquals($dedicatedMentorId, $this->executeDedicateMentorToParticipant());
    }
    public function test_dedicateMentorToParticipant_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinator(function (){
            $this->executeDedicateMentorToParticipant();
        });
    }
    public function test_dedicateMentorToParticipant_unmanageParticipant_forbidden()
    {
        $this->setAssetUnmanageable($this->participant);
        $this->assertUnmanageAsset(function (){
            $this->executeDedicateMentorToParticipant();
        }, 'participant');
    }
    public function test_dedicateMentorToParticipant_unmanageMentor_forbidden()
    {
        $this->setAssetUnmanageable($this->consultant);
        $this->assertUnmanageAsset(function (){
            $this->executeDedicateMentorToParticipant();
        }, 'consultant');
    }
    
    protected function executeCancelMentorDedication()
    {
        $this->setAssetManageable($this->dedicatedMentor);
        $this->coordinator->cancelMentorDedication($this->dedicatedMentor);
    }
    public function test_cancelMentorDedication()
    {
        $this->dedicatedMentor->expects($this->once())
                ->method('cancel');
        $this->executeCancelMentorDedication();
    }
    public function test_cancelMentorDedication_inactiveCoordinator_forbidden()
    {
        $this->coordinator->active = false;
        $this->assertInactiveCoordinator(function (){
            $this->executeCancelMentorDedication();
        });
    }
    public function test_cancelMentorDedication_unmanagerMentorDedication_forbidden()
    {
        $this->setAssetUnmanageable($this->dedicatedMentor);
        $this->assertUnmanageAsset(function (){
            $this->executeCancelMentorDedication();
        }, 'dedicated mentor');
    }
}
