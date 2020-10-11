<?php

namespace Notification\Domain\SharedModel;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\SharedModel\Mail\Recipient;
use Resources\Uuid;

class Mail
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

    public function __construct(string $id, string $senderMailAddress, string $senderName, string $subject, string $message,
            ?string $htmlMessage, string $recipientMailAddress, string $recipientName)
    {
        $this->id = $id;
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
        $id = Uuid::generateUuid4();
        $recipient = new Recipient($this, $id, $recipientMailAddress, $recipientName);
        $this->recipients->add($recipient);
    }

}
