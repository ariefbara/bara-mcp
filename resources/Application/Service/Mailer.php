<?php
namespace Resources\Application\Service;

use Resources\Domain\Model\Mail;

interface Mailer
{
    public function send(Mail $mail, string $senderName, string $senderAddress): void;
}

