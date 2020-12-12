<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\Model\Firm\Program\Consultant;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;

class ConsultantMeetingAttendee
{
    /**
     * 
     * @var Consultant
     */
    protected $consultant;

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
        $this->consultant->registerMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerAsNotificationRecipient(ContainNotification $notification): void
    {
        $this->consultant->registerNotificationRecipient($notification);
    }

}
