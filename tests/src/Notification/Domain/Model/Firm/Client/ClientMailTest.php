<?php

namespace Notification\Domain\Model\Firm\Client;

use Notification\Domain\ {
    Model\Firm\Client,
    SharedModel\Mail
};
use Tests\TestBase;

class ClientMailTest extends TestBase
{

    protected $client;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "new sender name",
            $subject = "new subject", $message = "new message", $htmlMessage = "new html message",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = $this->buildMockOfClass(Client::class);
    }

    public function test_construct_setProperties()
    {
        $clientMail = new TestableClientMail(
                $this->client, $this->id, $this->senderMailAddress, $this->senderName, $this->subject, $this->message,
                $this->htmlMessage, $this->recipientMailAddress, $this->recipientName);
        
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
