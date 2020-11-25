<?php

namespace Notification\Domain\Model\Firm\Manager;

use Notification\Domain\ {
    Model\Firm\Manager,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class ManagerMailTest extends TestBase
{

    protected $manager;
    protected $mailMessage;
    protected $id = "id", $senderMailAddress = "sender@email.org", $senderName = "sender name",
            $recipientMailAddress = "recipent@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = $this->buildMockOfClass(Manager::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }

    public function test_construct_setProperties()
    {

        $managerMail = new TestableManagerMail(
                $this->manager, $this->id, $this->senderMailAddress, $this->senderName, $this->mailMessage,
                $this->recipientMailAddress, $this->recipientName);
        
        $this->assertEquals($this->manager, $managerMail->manager);
        $this->assertEquals($this->id, $managerMail->id);
        $this->assertInstanceOf(Mail::class, $managerMail->mail);
    }

}

class TestableManagerMail extends ManagerMail
{

    public $manager;
    public $id;
    public $mail;

}
