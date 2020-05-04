<?php

namespace Query\Domain\Model\Shared;

use DateTimeImmutable;

class Notification
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message = null;

    /**
     *
     * @var bool
     */
    protected $read = false;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $notifiedTime;

    function getId(): string
    {
        return $this->id;
    }

    function getMessage(): string
    {
        return $this->message;
    }

    function isRead(): bool
    {
        return $this->read;
    }

    function getNotifiedTimeString(): string
    {
        return $this->notifiedTime->format('Y-m-d H:i:s');
    }

    protected function __construct()
    {
        ;
    }

}
