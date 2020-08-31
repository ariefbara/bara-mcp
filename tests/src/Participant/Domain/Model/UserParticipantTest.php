<?php

namespace Participant\Domain\Model;

use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $participant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = new TestableUserParticipant();
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant->participant = $this->participant;
    }
    
    public function test_quit_quitParticipant()
    {
        $this->participant->expects($this->once())
                ->method('quit');
        $this->userParticipant->quit();
    }
}

class TestableUserParticipant extends UserParticipant
{
    public $userId;
    public $id;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
