<?php

namespace Notification\Domain\SharedModel\Notification;

use DateTimeImmutable;
use Notification\Domain\ {
    Model\User,
    SharedModel\Notification
};
use Resources\DateTimeImmutableBuilder;

class UserNotificationRecipient
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
     * @var User
     */
    protected $user;

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

    public function __construct(Notification $notification, string $id, User $user)
    {
        $this->notification = $notification;
        $this->id = $id;
        $this->user = $user;
        $this->read = false;
        $this->notifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

}
