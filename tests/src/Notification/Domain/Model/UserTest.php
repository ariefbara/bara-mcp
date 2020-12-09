<?php

namespace Notification\Domain\Model;

use Notification\Domain\ {
    Model\User\UserMail,
    SharedModel\CanSendPersonalizeMail
};
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class UserTest extends TestBase
{
    protected $user;
    protected $name;
    protected $mailGenerator;
    protected $mailMessage, $modifiedGreetings, $modifiedUrl, $modifiedMail;
    protected $userMailId = "userMailId";

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new TestableUser();
        $this->name = $this->buildMockOfClass(PersonName::class);
        $this->user->name = $this->name;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedMail = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_getName_returnFullName()
    {
        $this->name->expects($this->once())
                ->method('getFullName');
        $this->user->getFullName();
    }
    
    protected function executeRegisterAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("appendRecipientFirstNameInGreetings")
                ->willReturn($this->modifiedMail);
        
        $this->user->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerAsMailRecipient_modifiedMailMessageGreetings()
    {
        $this->name->expects($this->once())
                ->method("getFirstName")
                ->willReturn($firstName = "first name");
        
        $this->mailMessage->expects($this->any())
                ->method("appendRecipientFirstNameInGreetings")
                ->with($firstName);
        
        $this->executeRegisterAsMailRecipient();
    }
    public function test_registerAsMailRecipient_addMailInMailGenerator()
    {
        $this->name->expects($this->once())
                ->method("getFullName")
                ->willReturn($fullName = "full name");
        $this->mailGenerator->expects($this->once())
                ->method("addMail")
                ->with($this->identicalTo($this->modifiedMail), $this->user->email, $fullName);
        $this->executeRegisterAsMailRecipient();
    }
    
    public function test_createActivationMail_returnUserMail()
    {
        $this->assertInstanceOf(UserMail::class, $this->user->createActivationMail($this->userMailId));
    }
    public function test_createResetPasswordMail_returnUserMail()
    {
        $this->assertInstanceOf(UserMail::class, $this->user->createResetPasswordMail($this->userMailId));
    }
}

class TestableUser extends User
{
    public $id;
    public $name;
    public $email = "user@email.org";
    
    function __construct()
    {
        parent::__construct();
    }
}
