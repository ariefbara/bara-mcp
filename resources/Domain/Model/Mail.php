<?php

namespace Resources\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Resources\{
    Application\Service\MailInterface,
    Domain\Model\Mail\DynamicAttachment,
    Domain\Model\Mail\Recipient,
    ValidationRule,
    ValidationService
};

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
     * @var string
     */
    protected $alternativeBody = null;

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

    private function setSubject(string $subject)
    {
        $errorDetails = "bad request: mail subject is required";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($subject, $errorDetails);
        $this->subject = $subject;
    }

    private function setBody(string $body)
    {
        $errorDetails = "bad request: mail body is required";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($body, $errorDetails);
        $this->body = $body;
    }

    public function __construct(string $subject, string $body, ?string $alternativeBody, Recipient $recipient)
    {
        $this->setSubject($subject);
        $this->setBody($body);
        $this->alternativeBody = $alternativeBody;

        $this->recipients = new ArrayCollection();
        $this->recipients->add($recipient);
        $this->dynamicAttachments = new ArrayCollection();
    }

    public function addRecipient(Recipient $recipient): void
    {
        $this->recipients->add($recipient);
    }

    public function addDynamicAttachment(DynamicAttachment $dynamicAttachment)
    {
        $this->dynamicAttachments->add($dynamicAttachment);
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

    /**
     *
     * @return Recipient[]
     */
    public function getRecipients()
    {
        return $this->recipients->getIterator();
    }

    /**
     *
     * @return DynamicAttachment[]
     */
    public function getDynamicAttachments()
    {
        return $this->dynamicAttachments->getIterator();
    }

    public function getSenderMailAddress(): string
    {
        
    }

    public function getSenderName(): string
    {
        
    }

}
