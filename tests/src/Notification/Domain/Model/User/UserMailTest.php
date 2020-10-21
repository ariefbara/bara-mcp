<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\ {
    Model\User,
    SharedModel\Mail
};
use Tests\TestBase;

class UserMailTest extends TestBase
{
    protected $user;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "new sender name",
            $subject = "new subject", $message = "new message", $htmlMessage = "new html message",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->buildMockOfClass(User::class);
    }
    
    public function test_construct_setProperties()
    {
        $userMail = new TestableUserMail(
                $this->user, $this->id, $this->senderMailAddress, $this->senderMailAddress, $this->subject, $this->message, $this->htmlMessage, $this->recipientMailAddress, $this->recipientName);
        
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
