<?php

namespace Notification\Domain\SharedModel\Mail;

use Notification\Domain\SharedModel\Mail;

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

    public function getRecipientMailAddress(): string
    {
        return $this->recipientMailAddress;
    }

    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    public function __construct(Mail $mail, string $id, string $recipientMailAddress, string $recipientName)
    {
        $this->mail = $mail;
        $this->id = $id;
        $this->recipientMailAddress = $recipientMailAddress;
        $this->recipientName = $recipientName;
        $this->sent = false;
        $this->attempt = 0;
    }

    public function getSenderMailAddress(): string
    {
        return $this->mail->getSenderMailAddress();
    }

    public function getSenderName(): string
    {
        return $this->mail->getSenderName();
    }

    public function getSubject(): string
    {
        return $this->mail->getSubject();
    }

    public function getMessage(): string
    {
        return $this->mail->getMessage();
    }

    public function getHtmlMessage(): ?string
    {
        return $this->mail->getHtmlMessage();
    }
    
    public function getIcalAttachment(): ?IcalAttachment
    {
        return $this->mail->getIcalAttachment();
    }

    public function sendSuccessful(): void
    {
        $this->sent = true;
    }

    public function increaseAttempt(): void
    {
        $this->attempt += 1;
    }

}
