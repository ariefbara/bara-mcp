<?php

namespace Resources\Application\Service;

interface MailInterface
{

    public function getSenderMailAddress(): string;

    public function getSenderName(): string;

    public function getSubject(): string;

    public function getBody(): string;

    public function getAlternativeBody(): ?string;
    
    /**
     * 
     * @return RecipientInterface[]
     */
    public function getRecipients();
    
    /**
     * 
     * @return DynamicAttachmentInterface[]
     */
    public function getDynamicAttachments();
}
