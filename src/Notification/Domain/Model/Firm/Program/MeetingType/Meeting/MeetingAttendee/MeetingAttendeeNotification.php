<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;

use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting\MeetingAttendee;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\ContainNotification;
use Notification\Domain\SharedModel\ContainNotificationForManager;

class MeetingAttendeeNotification implements ContainNotification, ContainNotificationForManager
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
     * @var Notification
     */
    protected $notification;

    function __construct(MeetingAttendee $meetingAttendee, string $id, string $message)
    {
//        $this->meetingAttendee = $meetingAttendee;
//        $this->id = $id;
//        $this->notification = $notification;
    }

    public function addClientRecipient(Client $client): void
    {
        
    }

    public function addManagerRecipient(Manager $manager): void
    {
        
    }

    public function addPersonnelRecipient(Personnel $personnel): void
    {
        
    }

    public function addUserRecipient(User $user): void
    {
        
    }

}
