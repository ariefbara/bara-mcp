<?php

namespace Notification\Domain\SharedModel;

interface CanSendPersonalizeMail
{
    public function addMail(MailMessage $mailMessage, string $recipientMailMessage, string $recipientName): void;
}
