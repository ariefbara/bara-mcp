<?php

namespace Firm\Domain\Model\Firm\Program;

use Resources\DateTimeImmutableBuilder;
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $participant;
    protected $inactiveParticipant;
    
    protected $clientParticipant;
    protected $userParticipant;


    protected $id = 'newParticipantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->participant = new TestableParticipant('id');
        $this->inactiveParticipant = new TestableParticipant('id');
        $this->inactiveParticipant->active = false;
        $this->inactiveParticipant->note = 'booted';
        
        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->participant->clientParticipant = $this->clientParticipant;
        
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->participant->userParticipant = $this->userParticipant;
    }
    
    public function test_construct_setProperties()
    {
        $participant = new TestableParticipant($this->id);
        $this->assertEquals($this->id, $participant->id);
        $this->assertEquals(DateTimeImmutableBuilder::buildYmdHisAccuracy(), $participant->enrolledTime);
        $this->assertTrue($participant->active);
        $this->assertNull($participant->note);
    }
    
    protected function executeBootout()
    {
        $this->participant->bootout();
    }
    public function test_bootout_setActiveFlagFalseAndNoteBooted()
    {
        $this->executeBootout();
        $this->assertFalse($this->participant->active);
        $this->assertEquals('booted', $this->participant->note);
    }
    public function test_bootout_alreadyInactive_forbiddenError()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeBootout();
        };
        $errorDetail = 'forbidden: participant already inactive';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }

    protected function executeReenroll()
    {
        $this->inactiveParticipant->reenroll();
    }
    public function test_reenroll_setActiveTrueAndNulledNote()
    {
        $this->executeReenroll();
        $this->assertTrue($this->inactiveParticipant->active);
        $this->assertNull($this->inactiveParticipant->note);
    }
    public function test_reenroll_activeParticipant_forbiddenError()
    {
        $operation = function (){
            $this->participant->reenroll();
        };
        $errorDetail = 'forbidden: already active participant';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeGetMailRecipient()
    {
        return $this->participant->getMailRecipient();
    }
    public function test_getMailRecipient_returnClientParticipantsGetClientMailRecipientResult()
    {
        $this->clientParticipant->expects($this->once())
                ->method('getClientMailRecipient');
        $this->executeGetMailRecipient();
    }
    public function test_getMailRecipient_emptyClientParticipant_returnUserParticipantsGetUserMailRecipientResult()
    {
        $this->participant->clientParticipant = null;
        
        $this->userParticipant->expects($this->once())
                ->method('getUserMailRecipient');
        $this->executeGetMailRecipient();
    }
    
    protected function executeGetParticipantName()
    {
        return $this->participant->getParticipantName();
    }
    public function test_getParticipantName_returnClientParticipantsGetClientNameResult()
    {
        $this->clientParticipant->expects($this->once())
                ->method('getClientName');
        $this->executeGetParticipantName();
    }
    public function test_getParticipantName_nullClientParticipant_returnUserParticipantsGetUserNameResult()
    {
        $this->participant->clientParticipant = null;
        
        $this->userParticipant->expects($this->once())
                ->method('getUserName');
        $this->executeGetParticipantName();
    }
}

class TestableParticipant extends Participant
{
    public $id;
    public $enrolledTime;
    public $active = true;
    public $note;
    public $clientParticipant;
    public $userParticipant;
}
