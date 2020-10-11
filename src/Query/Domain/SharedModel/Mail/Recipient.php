<?php

namespace Query\Domain\SharedModel\Mail;

use Query\Domain\SharedModel\Mail;

class Recipient
{

    /**
     *
     * @var Mail
     */
    protected $mail;

    /**
     *
     * @var string
     */
    protected $id;

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

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getRecipientMailAddress(): string
    {
        return $this->recipientMailAddress;
    }

    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    public function isSent(): bool
    {
        return $this->sent;
    }

    public function getAttempt(): int
    {
        return $this->attempt;
    }

    protected function __construct()
    {
        ;
    }

}
