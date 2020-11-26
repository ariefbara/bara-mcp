<?php

namespace Notification\Domain\Model\Firm\Personnel;

use Notification\Domain\ {
    Model\Firm\Personnel,
    SharedModel\Mail
};
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class PersonnelMailTest extends TestBase
{
    protected $personnel;
    protected $mailMessage;
    protected $id = "id", $senderMailAddress = "sender@email.org", $senderName = "sender name", 
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_construct_setProperties()
    {
        $personnelMail = new TestablePersonnelMail($this->personnel, $this->id, $this->senderMailAddress, 
                $this->senderName, $this->mailMessage, $this->recipientMailAddress, $this->recipientName);
        $this->assertEquals($this->personnel, $personnelMail->personnel);
        $this->assertEquals($this->id, $personnelMail->id);
        $this->assertInstanceOf(Mail::class, $personnelMail->mail);
    }
}

class TestablePersonnelMail extends PersonnelMail
{
    public $personnel;
    public $id;
    public $mail;
}
