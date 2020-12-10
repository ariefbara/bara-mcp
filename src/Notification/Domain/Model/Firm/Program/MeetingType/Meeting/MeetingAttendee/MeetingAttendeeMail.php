<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\SharedModel\Mail;

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
//        $this->meetingAttendee = $meetingAttendee;
//        $this->id = $id;
//        $this->mail = $mail;
    }

}
