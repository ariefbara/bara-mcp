<?php

namespace Notification\Application\Service;

use Notification\Domain\SharedModel\Mail\Recipient;

interface MailSender
{

    public function send(Recipient $recipient): void;
}
