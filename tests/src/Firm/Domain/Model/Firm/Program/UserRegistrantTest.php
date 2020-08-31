<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\ {
    Firm\Program,
    User
};
use Tests\TestBase;

class UserRegistrantTest extends TestBase
{
    protected $userRegistrant, $registrant;
    protected $program;
    protected $user;
    protected $id = 'userRegistrantId';
    
    protected $userParticipantId = 'userParticipantId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->user = $this->buildMockOfClass(User::class);
        
        $this->userRegistrant = new TestableUserRegistrant();
        $this->userRegistrant->program = $this->program;
        $this->userRegistrant->user = $this->user;
        
        $this->registrant = $this->buildMockOfClass(Registrant::class);
        $this->userRegistrant->registrant = $this->registrant;
    }
    
    public function test_accept_acceptRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method('accept');
        $this->userRegistrant->accept();
    }
    public function test_reject_rejectRegistrant()
    {
        $this->registrant->expects($this->once())
                ->method('reject');
        $this->userRegistrant->reject();
    }
    
    public function test_createParticipant_returnUserParticipant()
    {
        $userParticipant = new UserParticipant($this->program, $this->userParticipantId, $this->user);
        $this->assertEquals($userParticipant, $this->userRegistrant->createParticipant($this->userParticipantId));
    }
    
    public function test_userEquals_sameUser_returnTrue()
    {
        $this->assertTrue($this->userRegistrant->userEquals($this->user));
    }
    public function test_userIdEquals_differentUserid_returnFalse()
    {
        $this->assertFalse($this->userRegistrant->userEquals($this->buildMockOfClass(User::class)));
    }
}

class TestableUserRegistrant extends UserRegistrant
{
    public $program;
    public $id;
    public $user;
    public $registrant;
    
    function __construct()
    {
        parent::__construct();
    }
}
