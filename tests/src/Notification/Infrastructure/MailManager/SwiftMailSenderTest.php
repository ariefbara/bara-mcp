<?php

namespace Notification\Infrastructure\MailManager;

use Notification\Domain\SharedModel\Mail\Recipient;
use Swift_Mailer;
use Tests\TestBase;

class SwiftMailSenderTest extends TestBase
{

    protected $vendor;
    protected $swiftMailerSender;
    protected $recipient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->vendor = $this->buildMockOfClass(Swift_Mailer::class);
        $this->swiftMailerSender = new TestableSwiftMailSender($this->vendor);
        $this->recipient = $this->buildMockOfClass(Recipient::class);
        $this->recipient->expects($this->any())->method("getSenderMailAddress")->willReturn("sender@email.org");
        $this->recipient->expects($this->any())->method("getRecipientMailAddress")->willReturn("recipient@email.org");
    }
    
    protected function executeSend()
    {
        $this->swiftMailerSender->send($this->recipient);
    }
    public function test_send_executeVendorsSendMethod()
    {
        $this->vendor->expects($this->once())
                ->method("send");
        $this->executeSend();
    }
    public function test_send_succesfull_setRecipientSendSuccessful()
    {
        $this->vendor->expects($this->once())
                ->method("send")
                ->willReturn(1);
        $this->recipient->expects($this->once())
                ->method("sendSuccessful");
        $this->executeSend();
    }
    public function test_send_fail_dontSendRecipientSentStatus()
    {
        $this->vendor->expects($this->once())
                ->method("send")
                ->willReturn(0);
        $this->recipient->expects($this->never())
                ->method("sendSuccessful");
        $this->executeSend();
    }
    public function test_send_increateRecipientAttempt()
    {
        $this->recipient->expects($this->once())
                ->method("increaseAttempt");
        $this->executeSend();
    }

}

class TestableSwiftMailSender extends SwiftMailSender
{
    public $vendor;
}
