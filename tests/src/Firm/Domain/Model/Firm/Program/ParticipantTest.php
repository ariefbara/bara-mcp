<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\ {
    Client,
    Firm\Program
};
use Tests\TestBase;

class ParticipantTest extends TestBase
{
    protected $program, $client;
    protected $participant;
    
    protected $id;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->client = $this->buildMockOfClass(Client::class);
        
        $this->participant = new TestableParticipant($this->program, 'id', $this->client);
    }
    
    public function test_construct_setProperties()
    {
        $participant = new TestableParticipant($this->program, $this->id, $this->client);
        $this->assertEquals($this->program, $participant->program);
        $this->assertEquals($this->id, $participant->id);
        $this->assertEquals($this->client, $participant->client);
        $this->assertEquals($this->YmdHisStringOfCurrentTime(), $participant->acceptedTime->format('Y-m-d H:i:s'));
        $this->assertTrue($participant->active);
        $this->assertNull($participant->note);
    }
    
    protected function executeRemove()
    {
        $this->participant->remove();
    }
    public function test_remove_setActiveFlagFalse()
    {
        $this->executeRemove();
        $this->assertFalse($this->participant->active);
    }
    public function test_remove_setNoteRemoved()
    {
        $this->executeRemove();
        $this->assertEquals('removed', $this->participant->note);
    }
    public function test_remove_alreadyInactive_throwEx()
    {
        $this->participant->active = false;
        $operation = function (){
            $this->executeRemove();
        };
        $errorDetail = 'forbidden: participant already inactive';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
    protected function executeReActivateWithActiveSetFalse()
    {
        $this->participant->active = false;
        $this->participant->reActivate();
    }
    public function test_reActive_setActiveTrue()
    {
        $this->executeReActivateWithActiveSetFalse();
        $this->assertTrue($this->participant->active);
    }
    public function test_reActive_setNoteReactivated()
    {
        $this->executeReActivateWithActiveSetFalse();
        $this->assertEquals('reactivated', $this->participant->note);
    }
    public function test_reActive_alreadyActive_throwEx()
    {
        $operation = function (){
            $this->participant->reActivate();
        };
        $errorDetail = 'forbidden: already an active participant';
        $this->assertRegularExceptionThrowed($operation, 'Forbidden', $errorDetail);
    }
    
}

class TestableParticipant extends Participant
{
    public $program, $id, $client, $acceptedTime, $active, $note;
}
