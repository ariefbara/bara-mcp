<?php

namespace Notification\Domain\SharedModel\Notification;

use DateTimeImmutable;
use Notification\Domain\ {
    Model\Firm\Personnel,
    SharedModel\Notification
};
use Resources\DateTimeImmutableBuilder;

class PersonnelNotificationRecipient
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
     * @var Personnel
     */
    protected $personnel;

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

    public function __construct(Notification $notification, string $id, Personnel $personnel)
    {
        $this->notification = $notification;
        $this->id = $id;
        $this->personnel = $personnel;
        $this->read = false;
        $this->notifiedTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

}
