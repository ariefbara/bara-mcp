<?php

namespace Notification\Domain\Model\Firm\Program\MeetingType\Meeting;

use Notification\Domain\Model\Firm\Client;
use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program\MeetingType\Meeting;
use Notification\Domain\Model\User;
use Notification\Domain\SharedModel\Notification;
use Notification\Domain\SharedModel\ContainNotification;
use Notification\Domain\SharedModel\ContainNotificationForManager;

class MeetingNotification implements ContainNotification, ContainNotificationForManager
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
     * @var Notification
     */
    protected $notification;

    function __construct(Meeting $meeting, string $id, string $message)
    {
//        $this->meeting = $meeting;
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
