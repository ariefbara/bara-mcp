<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Notification\Domain\SharedModel\Mail;
use SharedContext\Domain\ValueObject\MailMessage;
use Tests\TestBase;

class MeetingMailTest extends TestBase
{

    protected $meeting;
    protected $meetingMail;
    protected $mailMessage, $mail;
    protected $id = "newId", $senderMailAddress = "sender@email.org", $senderName = "sender name",
            $recipientMailAddress = "recipient@email.org", $recipientName = "recipient name";
    protected $icalAttachmentContent = 'ical attachment content';

    protected function setUp(): void
    {
        parent::setUp();
        $this->meeting = $this->buildMockOfClass(Meeting::class);
        $this->mailMessage = $this->buildMockOfClass(MailMessage::class);
        
        $this->meetingMail = new TestableMeetingMail(
                $this->meeting, 'id', 'sender@email.org', 'sender name', $this->mailMessage, 'recipient@email.org', 'recipient name');
        $this->mail = $this->buildMockOfClass(Mail::class);
        $this->meetingMail->mail = $this->mail;
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
    
    public function test_setIcalAttachment_setMailIcalAttachment()
    {
        $this->mail->expects($this->once())
                ->method('setIcalAttachment')
                ->with($this->icalAttachmentContent);
        $this->meetingMail->setIcalAttachment($this->icalAttachmentContent);
    }

}

class TestableMeetingMail extends MeetingMail
{

    public $meeting;
    public $id;
    public $mail;

}
