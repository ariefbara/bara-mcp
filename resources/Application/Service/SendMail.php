<?php

namespace Resources\Application\Service;

use Resources\{
    Domain\Model\Mail, ValidationRule, ValidationService
};

class SendMail
{

    protected $mailer;
    protected $senderName;
    protected $senderAddress;

    private function setSenderName($senderName)
    {
        $errorDetail = 'bad request: to send mail, sender name is required';
        ValidationService::build()
            ->addRule(ValidationRule::notEmpty())
            ->execute($senderName, $errorDetail);
        $this->senderName = $senderName;
    }

    private function setSenderAddress($senderAddress)
    {
        $errorDetail = 'bad request: to send mail, sender address is required and must be a valid email address';
        ValidationService::build()
            ->addRule(ValidationRule::email())
            ->execute($senderAddress, $errorDetail);
        $this->senderAddress = $senderAddress;
    }

    public function __construct(Mailer $mailer, string $senderName, string $senderAddress)
    {
        $this->mailer = $mailer;
        $this->setSenderName($senderName);
        $this->setSenderAddress($senderAddress);
    }

    public function execute(Mail $mail): void
    {
        $this->mailer->send($mail, $this->senderName, $this->senderAddress);
    }

}
