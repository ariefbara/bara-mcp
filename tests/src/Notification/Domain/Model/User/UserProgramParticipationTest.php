<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\ {
    Model\User,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class UserProgramParticipationTest extends TestBase
{
    protected $userProgramParticipation;
    protected $user;
    protected $programParticipation;
    protected $mailGenerator;
    protected $mailMessage, $modifiedMailMessage;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userProgramParticipation = new TestableUserProgramParticipation();
        
        $this->user = $this->buildMockOfClass(User::class);
        $this->userProgramParticipation->user = $this->user;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedMailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }
    
    public function test_getUserFullName_returnUserGetFullNameResult()
    {
        $this->user->expects($this->once())
                ->method("getFullName");
        $this->userProgramParticipation->getUserFullName();
    }
    
    protected function executeRegisterUserAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("prependUrlPath")
                ->willReturn($this->modifiedMailMessage);
        
        $this->userProgramParticipation->registerUserAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerUserAsMailRecipient_prependUserProgramParticipationToMailMessage()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/program-participations/{$this->userProgramParticipation->id}");
        $this->executeRegisterUserAsMailRecipient();
    }
    public function test_registerUserAsMailRecipient_registerUserAsMailRecipient()
    {
        $this->user->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->mailGenerator, $this->modifiedMailMessage);
        $this->executeRegisterUserAsMailRecipient();
    }
    
    public function test_registerUserAsNotificationRecipient_addUserAsNotificationRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addUserRecipient")
                ->with($this->user);
        $this->userProgramParticipation->registerUserAsNotificationRecipient($this->notification);
    }
}

class TestableUserProgramParticipation extends UserProgramParticipation
{
    public $user;
    public $id = "userProgramParticipationId";
    public $programParticipation;
    
    function __construct()
    {
        parent::__construct();
    }
}
