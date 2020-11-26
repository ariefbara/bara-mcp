<?php

namespace Notification\Domain\SharedModel;

use SharedContext\Domain\ValueObject\MailMessage;

interface CanSendPersonalizeMail
{
    public function addMail(MailMessage $mailMessage, string $recipientMailMessage, string $recipientName): void;
}
