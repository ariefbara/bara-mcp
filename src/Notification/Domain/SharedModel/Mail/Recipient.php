<?php

namespace Notification\Domain\SharedModel\Mail;

use Notification\Domain\SharedModel\Mail;

class Recipient
{
    protected $mail;

    /**
     *
     * @var string
     */
    protected $recipientMailAddress;

    /**
     *
     * @var string
     */
    protected $recipientName;

    /**
     *
     * @var bool
     */
    protected $sent;

    /**
     *
     * @var int
     */
    protected $attempt;

    public function getRecipientMailAddress(): string
    {
        return $this->recipientMailAddress;
    }

    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    public function __construct(Mail $mail, string $recipientMailAddress, string $recipientName)
    {
        $this->recipientMailAddress = $recipientMailAddress;
        $this->recipientName = $recipientName;
        $this->sent = false;
        $this->attempt = 0;
    }

}
