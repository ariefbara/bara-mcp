<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\User;
use Tests\TestBase;

class UserRegistrantTest extends TestBase
{
    protected $userRegistrant;
    protected $user;
    protected $program;
    
    protected $participantId = 'participantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRegistrant = new TestableUserRegistrant();
        $this->user = $this->buildMockOfClass(User::class);
        $this->userRegistrant->user = $this->user;
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    public function test_createParticipant_returnParticipantForUser()
    {
        $participant = Participant::participantForUser($this->program, $this->participantId, $this->userRegistrant->user);
        
        $this->assertEquals($participant, $this->userRegistrant->createParticipant($this->program, $this->participantId));
    }
    
    public function test_userEquals_sameUser_returnTrue()
    {
        $this->assertTrue($this->userRegistrant->userEquals($this->user));
    }
    public function test_userEquals_differentUser_returnFalse()
    {
        $user = clone $this->user;
        $this->assertFalse($this->userRegistrant->userEquals($user));
    }
    
}

class TestableUserRegistrant extends UserRegistrant
{
    public $registrant;
    public $id;
    public $user;
    
    function __construct()
    {
        parent::__construct();
    }
}
