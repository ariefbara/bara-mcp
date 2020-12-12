<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Notification\Domain\SharedModel\Mail;
use SharedContext\Domain\ValueObject\MailMessage;

class MeetingMail
{

    /**
     * 
     * @var Meeting
     */
    protected $meeting;

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

    function __construct(Meeting $meeting, string $id, string $senderMailAddress, string $senderName,
            MailMessage $mailMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->meeting = $meeting;
        $this->id = $id;
        $this->mail = new Mail($id, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
