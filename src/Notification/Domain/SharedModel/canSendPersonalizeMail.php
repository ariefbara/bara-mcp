<?php

namespace Notification\Domain\SharedModel;

interface canSendPersonalizeMail
{
    public function addMail(MailMessage $mailMessage, string $recipientMailMessage, string $recipientName): void;
}
