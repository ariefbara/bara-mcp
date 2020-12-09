<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\SharedModel\Mail;
use SharedContext\Domain\ValueObject\MailMessage;

class MeetingAttendeeMail
{

    /**
     * 
     * @var MeetingAttendee
     */
    protected $meetingAttendee;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Mail
     */
    protected $mail;

    function __construct(MeetingAttendee $meetingAttendee, string $id, string $senderMailAddress, string $senderName,
            MailMessage $mailMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->meetingAttendee = $meetingAttendee;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
