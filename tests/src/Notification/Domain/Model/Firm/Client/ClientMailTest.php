<?php

namespace Notification\Domain\Model\Firm\Client;

use Notification\Domain\ {
    Model\Firm\Client,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ClientMailTest extends TestBase
{

    protected $client;
    protected $mailMessage;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "new sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }

    public function test_construct_setProperties()
    {
        $clientMail = new TestableClientMail(
                $this->client, $this->id, $this->senderMailAddress, $this->senderName, $this->mailMessage, 
                $this->recipientMailAddress, $this->recipientName);
        
        $this->assertEquals($this->client, $clientMail->client);
        $this->assertEquals($this->id, $clientMail->id);
        $this->assertInstanceOf(Mail::class, $clientMail->mail);
    }

}

class TestableClientMail extends ClientMail
{

    public $client;
    public $id;
    public $mail;

}
