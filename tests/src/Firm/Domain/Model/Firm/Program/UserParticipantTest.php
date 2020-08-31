<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\ {
    Firm\Program,
    User
};
use Resources\ {
    Application\Service\Mailer,
    Domain\ValueObject\PersonName
};
use Tests\TestBase;

class UserParticipantTest extends TestBase
{
    protected $userParticipant;
    protected $participant;
    
    protected $program;
    protected $id = 'newUserParticipantId', $userId = 'newUserId';
    
    protected $user;
    protected $userRegistrant;
    
    protected $mailer;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->user = $this->buildMockOfClass(User::class);
        
        $this->userParticipant = new TestableUserParticipant($this->program, 'id', $this->user);
        
        $this->participant = $this->buildMockOfClass(Participant::class);
        $this->userParticipant->participant = $this->participant;
        
        $this->userRegistrant = $this->buildMockOfClass(UserRegistrant::class);
        
        $this->user->expects($this->any())->method('getEmail')->willReturn('user@email.org');
        $this->user->expects($this->any())->method('getPersonName')->willReturn(new PersonName('hadi', 'pranoto'));
        
        $this->mailer = $this->buildMockOfInterface(Mailer::class);
    }
    
    public function test_construct_setProperties()
    {
        $userParticipant = new TestableUserParticipant($this->program, $this->id, $this->user);
        $this->assertEquals($this->program, $userParticipant->program);
        $this->assertEquals($this->id, $userParticipant->id);
        $this->assertEquals($this->user, $userParticipant->user);
        
        $participant = new Participant($this->id);
        $this->assertEquals($participant, $userParticipant->participant);
    }
    
    public function test_reenroll_reenrollParticipant()
    {
        $this->participant->expects($this->once())
                ->method('reenroll');
        $this->userParticipant->reenroll();
    }
    public function test_bootout_bootParticipantOut()
    {
        $this->participant->expects($this->once())
                ->method('bootout');
        $this->userParticipant->bootout();
    }
    
    public function test_correspondWithRegistrant_returnUserRegistrantUserIdEqualsMethodResult()
    {
        $this->userRegistrant->expects($this->once())
                ->method('userEquals')
                ->with($this->user)
                ->willReturn(true);
        $this->assertTrue($this->userParticipant->correspondWithRegistrant($this->userRegistrant));
    }
    
    public function test_sendRegistrationAcceptedMail_sendMailToMailer()
    {
        
        $this->mailer->expects($this->once())
                ->method('send');
        $this->userParticipant->sendRegistrationAcceptedMail($this->mailer);
    }
    
    public function test_getUserMailRecipient_returnUsersGetMailRecipientResult()
    {
        $this->user->expects($this->once())
                ->method('getMailRecipient');
        $this->userParticipant->getUserMailRecipient();
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
    public $program;
    public $id;
    public $user;
    public $participant;
}
