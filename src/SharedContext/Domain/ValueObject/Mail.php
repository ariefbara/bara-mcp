<?php

namespace SharedContext\Domain\ValueObject;

class Mail
{

    protected $sender;
    protected $subject;
    protected $body;
    protected $recipients;
    protected $attachments;

    public function send($mailer): void
    {
        $mailer->setSubject($this->subject);
        $mailer->setBody($this->body);
    }

}
