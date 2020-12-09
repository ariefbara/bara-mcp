<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\Model\Firm\Program\Participant;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;

class ParticipantMeetingAttendee
{

    /**
     * 
     * @var Participant
     */
    protected $participant;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var MeetingAttendee
     */
    protected $meetingAttendee;

    protected function __construct()
    {
        
    }
    
    public function registerAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $this->participant->registerMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerAsNotificationRecipient(ContainNotification $notification): void
    {
        $this->participant->registerNotificationRecipient($notification);
    }

}
