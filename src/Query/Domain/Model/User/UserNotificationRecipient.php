<?php

namespace Query\Domain\Model\User;

use DateTimeImmutable;
use Query\Domain\{
    Model\User,
    SharedModel\Notification
};

class UserNotificationRecipient
{

    /**
     *
     * @var User
     */
    protected $user;

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
     * @var bool
     */
    protected $read;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $notifiedTime;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isRead(): bool
    {
        return $this->read;
    }

    protected function __construct()
    {
        ;
    }

    public function getNotifiedTimeString(): string
    {
        return $this->notifiedTime->format("Y-m-d H:i:s");
    }

    public function getMessage(): string
    {
        return $this->notification->getMessage();
    }

    public function getConsultationRequestNotification(): ?ConsultationRequestNotification
    {
        return $this->notification->getConsultationRequestNotification();
    }

}
