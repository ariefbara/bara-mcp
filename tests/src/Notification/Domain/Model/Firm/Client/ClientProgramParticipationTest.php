<?php

namespace Notification\Domain\Model\Firm\Client;

use Notification\Domain\ {
    Model\Firm\Client,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ClientProgramParticipationTest extends TestBase
{
    protected $clientProgramParticipation;
    protected $client;
    protected $programParticipation;
    protected $mailGenerator;
    protected $mailMessage, $modifiedMailMessage;
    protected $notification;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clientProgramParticipation = new TestableClientProgramParticipation();
        
        $this->client = $this->buildMockOfClass(Client::class);
        $this->clientProgramParticipation->client = $this->client;
        
        $this->mailGenerator = $this->buildMockOfInterface(CanSendPersonalizeMail::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        $this->modifiedMailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->notification = $this->buildMockOfInterface(ContainNotification::class);
    }
    
    public function test_getClientFullName_returnClientGetFullNameResult()
    {
        $this->client->expects($this->once())
                ->method("getFullName");
        $this->clientProgramParticipation->getClientFullName();
    }
    
    protected function executeRegisterClientAsMailRecipient()
    {
        $this->mailMessage->expects($this->any())
                ->method("prependUrlPath")
                ->willReturn($this->modifiedMailMessage);
        
        $this->clientProgramParticipation->registerClientAsMailRecipient($this->mailGenerator, $this->mailMessage);
    }
    public function test_registerClientAsMailRecipient_prependClientProgramParticipationToMailMessage()
    {
        $this->mailMessage->expects($this->once())
                ->method("prependUrlPath")
                ->with("/program-participations/{$this->clientProgramParticipation->id}");
        $this->executeRegisterClientAsMailRecipient();
    }
    public function test_registerClientAsMailRecipient_registerClientAsMailRecipient()
    {
        $this->client->expects($this->once())
                ->method("registerAsMailRecipient")
                ->with($this->mailGenerator, $this->identicalTo($this->modifiedMailMessage));
        $this->executeRegisterClientAsMailRecipient();
    }
    
    public function test_registerClientAsNotificationRecipient_addClientAsNotificationRecipient()
    {
        $this->notification->expects($this->once())
                ->method("addClientRecipient")
                ->with($this->client);
        $this->clientProgramParticipation->registerClientAsNotificationRecipient($this->notification);
    }
}

class TestableClientProgramParticipation extends ClientProgramParticipation
{
    public $client;
    public $id;
    public $programParticipation;
    
    function __construct()
    {
        parent::__construct();
    }
}
