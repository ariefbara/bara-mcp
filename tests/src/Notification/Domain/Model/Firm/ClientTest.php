<?php

namespace Notification\Domain\Model\Firm;

use Notification\Domain\SharedModel\ {
    CanSendPersonalizeMail,
    MailMessage
};
use Resources\Domain\ValueObject\PersonName;
use Tests\TestBase;

class ClientTest extends TestBase
{
    protected $client;
    protected $name;
    protected $mailGenerator;
    protected $mailMessage, $modifiedGreetings, $modifiedUrl;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new TestableClient();
        $this->name = $this->buildMockOfClass(PersonName::class);
        $this->client->name = $this->name;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedGreetings = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedUrl = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_getName_returnFullName()
    {
        $this->name->expects($this->once())
                ->method('getFullName');
        $this->client->getFullName();
    }
    protected function executeRegisterAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("appendRecipientFirstNameInGreetings")
                ->willReturn($this->modifiedGreetings);
        
        $this->client->registerAsMailRecipient($this->mailGenerator, $this->mailMessage);
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
                ->with($this->modifiedGreetings, $this->client->email, $fullName);
        $this->executeRegisterAsMailRecipient();
    }
}

class TestableClient extends Client
{
    public $firm;
    public $id;
    public $name;
    public $email = "client@email.org";
    
    function __construct()
    {
        parent::__construct();
    }
}
