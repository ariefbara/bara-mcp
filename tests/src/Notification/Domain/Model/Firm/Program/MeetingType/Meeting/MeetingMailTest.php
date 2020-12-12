<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Notification\Domain\SharedModel\Mail;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class MeetingMailTest extends TestBase
{

    protected $meeting;
    protected $mailMessage;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }

    public function test_construct_setProperties()
    {
        $meetingMail = new TestableMeetingMail(
                $this->meeting, $this->id, $this->senderMailAddress, $this->senderName, $this->mailMessage,
                $this->recipientMailAddress, $this->recipientName);
        $this->assertEquals($this->meeting, $meetingMail->meeting);
        $this->assertEquals($this->id, $meetingMail->id);
        $this->assertInstanceOf(Mail::class, $meetingMail->mail);
    }

}

class TestableMeetingMail extends MeetingMail
{

    public $meeting;
    public $id;
    public $mail;

}
