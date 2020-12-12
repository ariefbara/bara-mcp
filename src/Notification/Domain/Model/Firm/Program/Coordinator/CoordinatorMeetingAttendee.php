<?php

namespace Notification\Domain\Model\Firm\Program\Coordinator;

use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;

class CoordinatorMeetingAttendee
{

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

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
        $this->coordinator->registerAsMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerAsNotificationRecipient(ContainNotification $notification): void
    {
        $this->coordinator->registerAsNotificationRecipient($notification);
    }

}
