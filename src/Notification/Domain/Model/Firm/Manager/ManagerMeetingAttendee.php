<?php

namespace Notification\Domain\Model\Firm\Manager;

use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationForAllUser;
use SharedContext\Domain\ValueObject\MailMessage;

class ManagerMeetingAttendee
{

    /**
     * 
     * @var Manager
     */
    protected $manager;

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
        $this->manager->registerAsMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerAsNotificationRecipient(ContainNotificationForAllUser $notification): void
    {
        $notification->addManagerRecipient($this->manager);
    }

}
