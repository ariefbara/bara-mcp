<?php

namespace Notification\Domain\Model;

use Resources\Application\Service\MailInterface;

class ConsultationRequestMail implements MailInterface, HasDynamicLinkInMessageInterface
{
    protected $senderName;
    protected $senderMailAddress;

    public function getAlternativeBody(): ?string
    {
        
    }

    public function getBody(): string
    {
        
    }

    public function getDynamicAttachments()
    {
        
    }

    public function getRecipients()
    {
        
    }

    public function getSenderMailAddress(): string
    {
        
    }

    public function getSenderName(): string
    {
        
    }

    public function getSubject(): string
    {
        
    }

    public function appendRecipientNameInGreetings(string $recipientFirstName): void
    {
        
    }

    public function prependApiPath(string $apiPath): void
    {
        
    }

}
