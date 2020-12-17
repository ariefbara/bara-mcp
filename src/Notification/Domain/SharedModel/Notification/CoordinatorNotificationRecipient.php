<?php

namespace Notification\Domain\SharedModel\Notification;

use DateTimeImmutable;
use Notification\Domain\Model\Firm\Program\Coordinator;
use Notification\Domain\SharedModel\Notification;

class CoordinatorNotificationRecipient
{

    /**
     * 
     * @var Notification
     */
    protected $notification;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var Coordinator
     */
    protected $coordinator;

    /**
     * 
     * @var bool
     */
    protected $read;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $notifiedTime;
    
    function __construct(Notification $notification, string $id, Coordinator $coordinator)
    {
        $this->notification = $notification;
        $this->id = $id;
        $this->coordinator = $coordinator;
        $this->read = false;
        $this->notifiedTime = \Resources\DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }


}
