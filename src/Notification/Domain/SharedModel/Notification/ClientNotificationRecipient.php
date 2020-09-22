<?php

namespace Notification\Domain\SharedModel\Notification;

use Notification\Domain\{
    Model\Firm\Client,
    SharedModel\Notification
};

class ClientNotificationRecipient
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
     * @var Client
     */
    protected $client;

    /**
     *
     * @var bool
     */
    protected $read;

}
