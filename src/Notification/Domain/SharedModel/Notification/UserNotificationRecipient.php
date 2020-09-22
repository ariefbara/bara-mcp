<?php

namespace Notification\Domain\SharedModel\Notification;

use Notification\Domain\ {
    Model\User,
    SharedModel\Notification
};

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

}
