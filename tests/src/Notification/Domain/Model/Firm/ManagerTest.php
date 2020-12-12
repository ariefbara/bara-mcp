<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\Model\Firm;
use Notification\Domain\Model\Firm\Manager\ManagerMail;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationForAllUser;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ManagerTest extends TestBase
{
    protected $manager;
    protected $firm;
    protected $mailGenerator, $mailMessage, $modifiedGreetingMailMessage, $modifiedUrlMailMessage;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = new TestableManager();
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->manager->firm = $this->firm;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->notification = $this->buildMockOfInterface(ContainNotificationForAllUser::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedGreetingMailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedUrlMailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_createResetPasswordMail_returnManagerMail()
    {
        $this->assertInstanceOf(ManagerMail::class, $this->manager->createResetPasswordMail("managerMailId"));
    }
    
    protected function executeRegisterAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("appendRecipientFirstNameInGreetings")
                ->willReturn($this->modifiedGreetingMailMessage);
        $this->modifiedGreetingMailMessage->expects($this->any())
                ->method("prependUrlPath")
                ->willReturn($this->modifiedUrlMailMessage);
        $this->manager->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerAsMailRecipient_appendSaluataionInMailMessageGreetings()
    {
        $this->mailMessage->expects($this->once())
                ->method("appendRecipientFirstNameInGreetings")
                ->with($this->manager->name);
        $this->executeRegisterAsMailRecipient();
    }
    public function test_registerAsMailRecipient_prependManagerUriInMailMessage()
    {
        $this->modifiedGreetingMailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/manager");
        $this->executeRegisterAsMailRecipient();
    }
    public function test_registerAsMailRegister_addMailInMailGenerator()
    {
        $this->mailGenerator->expects($this->once())
                ->method("addMail")
                ->with($this->identicalTo($this->modifiedUrlMailMessage, $this->manager->email, $this->manager->name));
        $this->executeRegisterAsMailRecipient();
    }
    
}

class TestableManager extends Manager
{
    public $firm;
    public $id;
    public $name = "manager name";
    public $email = "manager@email.org";
    public $resetPasswordCode = "resetPasswordCode";
    public $resetPasswordCodeExpiredTime;
    
    function __construct()
    {
        parent::__construct();
    }
}
