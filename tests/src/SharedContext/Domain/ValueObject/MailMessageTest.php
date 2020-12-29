<?php

namespace SharedContext\Domain\ValueObject;

use Tests\TestBase;

class MailMessageTest extends TestBase
{

    protected $mailMessage;
    protected $subject = "new subject";
    protected $logoPath = "http://path/to/logo.jpg";
    protected $greetings = "new greetings";
    protected $mainMessage = "new main message";
    protected $domain = "new-domain.com";
    protected $urlPath = "/new-entity/newEntityId";
    protected $recipientFirstName = "adi";

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailMessage = new TestableMailMessage("subject", "greetings", "main message", "domain.com",
                "/entity/entityId", "http://path/to/logo.jpg");
    }

    public function test_construct_setProperties()
    {
        $mailMessage = new TestableMailMessage($this->subject, $this->greetings, $this->mainMessage, $this->domain,
                $this->urlPath, $this->logoPath);
        $this->assertEquals($this->subject, $mailMessage->subject);
        $this->assertEquals($this->greetings, $mailMessage->greetings);
        $this->assertEquals($this->mainMessage, $mailMessage->mainMessage);
        $this->assertEquals($this->domain, $mailMessage->domain);
        $this->assertEquals($this->urlPath, $mailMessage->urlPath);
        $this->assertEquals($this->logoPath, $mailMessage->logoPath);
    }

    public function test_appendRecipientFirstNameInGreetings_returnNewMailMessageWithModifiedGreetings()
    {
        $greetings = $this->mailMessage->greetings . " $this->recipientFirstName";
        $newMessage = new TestableMailMessage(
                $this->mailMessage->subject, $greetings, $this->mailMessage->mainMessage, $this->mailMessage->domain,
                $this->mailMessage->urlPath, $this->logoPath);
        $this->assertEquals(
                $newMessage, $this->mailMessage->appendRecipientFirstNameInGreetings($this->recipientFirstName));
    }

    public function test_prependUrlPath_returnNewMailMessageWithModifiedUrlPath()
    {
        $urlPath = $this->urlPath . $this->mailMessage->urlPath;
        $newMessage = new TestableMailMessage(
                $this->mailMessage->subject, $this->mailMessage->greetings, $this->mailMessage->mainMessage,
                $this->mailMessage->domain, $urlPath, $this->logoPath);
        $this->assertEquals(
                $newMessage, $this->mailMessage->prependUrlPath($this->urlPath));
    }

    public function test_appendUrlPath_returnNewMailMessageWithModifiedUrlPath()
    {
        $urlPath = $this->mailMessage->urlPath . $this->urlPath;
        $newMessage = new TestableMailMessage(
                $this->mailMessage->subject, $this->mailMessage->greetings, $this->mailMessage->mainMessage,
                $this->mailMessage->domain, $urlPath, $this->logoPath);
        $this->assertEquals(
                $newMessage, $this->mailMessage->appendUrlPath($this->urlPath));
    }

    public function test_getTextMessage_returnTextMessage()
    {
        $textMessage = <<<_MESSAGE
{$this->mailMessage->greetings},

{$this->mailMessage->mainMessage}

{$this->mailMessage->domain}{$this->mailMessage->urlPath}
_MESSAGE;
        $this->assertEquals($textMessage, $this->mailMessage->getTextMessage());
    }

/*
    public function test_getHtmlMessage_returnHtmlMessage()
    {
        $textMessage = <<<_MESSAGE
<p>{$this->mailMessage->greetings},</p>

<p>{$this->mailMessage->mainMessage}</p>

<p>{$this->mailMessage->domain}{$this->mailMessage->urlPath}</p>
_MESSAGE;
        $this->assertEquals($textMessage, $this->mailMessage->getHtmlMessage());
    }
 * 
 */

}

class TestableMailMessage extends MailMessage
{

    public $subject;
    public $greetings;
    public $mainMessage;
    public $domain;
    public $urlPath;
    public $logoPath;
}
