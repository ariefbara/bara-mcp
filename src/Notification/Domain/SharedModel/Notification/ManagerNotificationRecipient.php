<?php

namespace Notification\Domain\SharedModel\Notification;

use DateTimeImmutable;
use Notification\Domain\Model\Firm\Manager;
use Notification\Domain\SharedModel\Notification;

class ManagerNotificationRecipient
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
     * @var Manager
     */
    protected $manager;

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
    
    function __construct(Notification $notification, string $id, Manager $manager)
    {
        $this->notification = $notification;
        $this->id = $id;
        $this->manager = $manager;
        $this->read = false;
        $this->notifiedTime = \Resources\DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }


}
