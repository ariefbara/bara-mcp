<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\SharedModel\Mail;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class MeetingAttendeeMailTest extends TestBase
{
    protected $meetingAttendee;
    protected $mailMessage;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";

    protected function setUp(): void
    {
        parent::setUp();
        $this->meetingAttendee = $this->buildMockOfClass(MeetingAttendee::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
    }
    
    public function test_construct_setProperties()
    {
        $meetingAttendeeMail = new TestableMeetingAttendeeMail(
                $this->meetingAttendee, $this->id, $this->senderMailAddress, $this->senderName, $this->mailMessage,
                $this->recipientMailAddress, $this->recipientName);
        $this->assertEquals($this->meetingAttendee, $meetingAttendeeMail->meetingAttendee);
        $this->assertEquals($this->id, $meetingAttendeeMail->id);
        $this->assertInstanceOf(Mail::class, $meetingAttendeeMail->mail);
    }
}

class TestableMeetingAttendeeMail extends MeetingAttendeeMail
{
    public $meetingAttendee;
    public $id;
    public $mail;
}
