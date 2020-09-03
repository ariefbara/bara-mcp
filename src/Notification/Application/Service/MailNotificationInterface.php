<?php

namespace Notification\Application\Service;

use Resources\Application\Service\Mailer;

interface MailNotificationInterface
{
    public function send(Mailer $mailer): void;
}
