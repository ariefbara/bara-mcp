<?php

namespace Notifier\Application\Service;

interface CanBeMailedInterface
{

    public function getSenderName(): string;

    public function getSenderMailAddress(): string;

    public function getSubject(): string;

    public function getBody(): string;

    public function getAlternativeBody(): string;

    /**
     * 
     * @return MailRecipientInterface[]
     */
    public function getMailRecipientsIterator(): \Traversable;

    /**
     * 
     * @return MailAttachmentInterface[]
     */
    public function getMailAttachmentsIterator(): \Traversable;
}
