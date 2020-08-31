<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\Model\User;
use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $user;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipant = new TestableUserParticipant();
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->userParticipant->user = $this->user;
    }
    public function test_getUserName_returnUsersGetNameResult()
    {
        $this->user->expects($this->once())
                ->method('getName');
        $this->userParticipant->getUserName();
    }
}

class TestableUserParticipant extends UserParticipant
{
    public $id;
    public $user;
    public $participant;
    
    function __construct()
    {
        parent::__construct();
    }
}
