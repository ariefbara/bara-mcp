<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;

class UserRegistrantTest extends TestBase
{
    protected $userRegistrant;
    protected $program;
    
    protected $participantId = 'participantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRegistrant = new TestableUserRegistrant();
        $this->userRegistrant->userId = 'userId';
        
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    public function test_createParticipant_returnParticipantForUser()
    {
        $participant = Participant::participantForUser($this->program, $this->participantId, $this->userRegistrant->userId);
        
        $this->assertEquals($participant, $this->userRegistrant->createParticipant($this->program, $this->participantId));
    }
    
    public function test_userIdEquals_sameUserId_returnTrue()
    {
        $this->assertTrue($this->userRegistrant->userIdEquals($this->userRegistrant->userId));
    }
    public function test_userIdEquals_differentUserId_returnFalse()
    {
        $this->assertFalse($this->userRegistrant->userIdEquals('differentId'));
    }
    
}

class TestableUserRegistrant extends UserRegistrant
{
    public $registrant;
    public $id;
    public $userId;
    
    function __construct()
    {
        parent::__construct();
    }
}
