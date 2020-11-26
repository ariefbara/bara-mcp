<?php

namespace Notification\Domain\SharedModel;

use Doctrine\Common\Collections\ArrayCollection;
use Notification\Domain\SharedModel\Mail\Recipient;
use Resources\Uuid;
use SharedContext\Domain\ValueObject\MailMessage;

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
     * @var MailMessage
     */
    protected $message;

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
        return $this->message->getSubject();
    }

    public function getMessage(): string
    {
        return $this->message->getTextMessage();
    }

    public function getHtmlMessage(): ?string
    {
        return $this->message->getHtmlMessage();
    }

    /**
     * 
     * @return Recipient[]
     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    public function __construct(string $id, string $senderMailAddress, string $senderName, MailMessage $mailMessage,
            string $recipientMailAddress, string $recipientName)
    {
        $this->id = $id;
        $this->senderMailAddress = $senderMailAddress;
        $this->senderName = $senderName;
        $this->message = $mailMessage;
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
