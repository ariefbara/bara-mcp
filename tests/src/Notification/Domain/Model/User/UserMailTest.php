<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\ {
    Model\User,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class UserMailTest extends TestBase
{
    protected $user;
    protected $mailMessage;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "new sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->buildMockOfClass(User::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_construct_setProperties()
    {
        $userMail = new TestableUserMail(
                $this->user, $this->id, $this->senderMailAddress, $this->senderMailAddress, $this->mailMessage, 
                $this->recipientMailAddress, $this->recipientName);
        
        $this->assertEquals($this->user, $userMail->user);
        $this->assertEquals($this->id, $userMail->id);
        $this->assertInstanceOf(Mail::class, $userMail->mail);
    }
}

class TestableUserMail extends UserMail
{
    public $user;
    public $id;
    public $mail;
}
