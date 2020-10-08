<?php

namespace Notification\Domain\SharedModel;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\SharedModel\Mail\Recipient;

class Mail
{

    /**
     *
     * @var string
     */
    protected $senderMailAddress;

    /**
     *
     * @var string
     */
    protected $senderName;

    /**
     *
     * @var string
     */
    protected $subject;

    /**
     *
     * @var string
     */
    protected $message;

    /**
     *
     * @var string|null
     */
    protected $htmlMessage;

    /**
     *
     * @var ArrayCollection
     */
    protected $recipients;

    public function getSenderMailAddress(): string
    {
        return $this->senderMailAddress;
    }

    public function getSenderName(): string
    {
        return $this->senderName;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getHtmlMessage(): ?string
    {
        return $this->htmlMessage;
    }

    /**
     * 
     * @return Recipient[]
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    public function __construct(string $senderMailAddress, string $senderName, string $subject, string $message,
            ?string $htmlMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->senderMailAddress = $senderMailAddress;
        $this->senderName = $senderName;
        $this->subject = $subject;
        $this->message = $message;
        $this->htmlMessage = $htmlMessage;
        $this->recipients = new ArrayCollection();
        $this->addRecipient($recipientMailAddress, $recipientName);
    }

    public function addRecipient(string $recipientMailAddress, string $recipientName): void
    {
        $recipient = new Recipient($recipientMailAddress, $recipientName);
        $this->recipients->add($recipient);
    }

}
