<?php

namespace Notification\Domain\SharedModel\Mail;

class Recipient
{

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

    public function __construct(string $recipientMailAddress, string $recipientName)
    {
        $this->recipientMailAddress = $recipientMailAddress;
        $this->recipientName = $recipientName;
    }

}
