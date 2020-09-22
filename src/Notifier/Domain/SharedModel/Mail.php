<?php

namespace Notifier\Domain\SharedModel;

use Doctrine\Common\Collections\ArrayCollection;
use Resources\Application\Service\MailInterface;

class Mail implements MailInterface
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
    protected $body;

    /**
     *
     * @var string||null
     */
    protected $alternativeBody;

    /**
     *
     * @var ArrayCollection
     */
    protected $recipients;

    /**
     *
     * @var ArrayCollection
     */
    protected $dynamicAttachments;

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

    public function getBody(): string
    {
        return $this->body;
    }

    public function getAlternativeBody(): ?string
    {
        return $this->alternativeBody;
    }

    public function getRecipients()
    {
        return $this->recipients;
    }

    public function getDynamicAttachments()
    {
        return $this->dynamicAttachments;
    }

    public function __construct(
            string $senderMailAddress, string $senderName, string $subject, string $body, ?string $alternativeBody,
            string $recipientName, string $recipientMailAddress)
    {
        $this->senderMailAddress = $senderMailAddress;
        $this->senderName = $senderName;
        $this->subject = $subject;
        $this->body = $body;
        $this->alternativeBody = $alternativeBody;
        $this->$this->addRecipient($recipientName, $recipientMailAddress);
    }

    public function updateBody(string $body): void
    {
        
    }

    public function addRecipient(string $name, string $mailAddress): void
    {
        
    }

}
