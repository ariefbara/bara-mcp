<?php
namespace Resources\Application\Service;


interface Mailer
{
    public function send(SenderInterface $sender, MailMessageInterface $mailMessage, RecipientInterface $recipient): void;
}

