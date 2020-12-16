<?php

namespace Notification\Domain\Model;

use Config\BaseConfig;
use DateTimeImmutable;
use Notification\Domain\Model\User\UserMail;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\MailMessage;
use SharedContext\Domain\ValueObject\MailMessageBuilder;

class User
{

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var PersonName
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $email;
    
    /**
     *
     * @var string|null
     */
    protected $activationCode;

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $activationCodeExpiredTime;

    /**
     *
     * @var string|null
     */
    protected $resetPasswordCode;

    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $resetPasswordCodeExpiredTime;

    protected function __construct()
    {
        ;
    }

    public function getFullName(): string
    {
        return $this->name->getFullName();
    }

    public function registerAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $modifiedMailMessage = $mailMessage->appendRecipientFirstNameInGreetings($this->name->getFirstName());

        $mailGenerator->addMail($modifiedMailMessage, $this->email, $this->name->getFullName());
    }

    public function createActivationMail(string $userMailId): UserMail
    {
        $domain = BaseConfig::KONSULTA_MAIN_URL;
        $urlPath = "/user-account/activate/{$this->email}/{$this->activationCode}}";
        $logoPath = BaseConfig::KONSULTA_LOGO_PATH;
        
        $senderMailAddress = BaseConfig::MAIL_SENDER_ADDRESS;
        $senderName = BaseConfig::MAIL_SENDER_NAME;
        $mailMessage = MailMessageBuilder::buildAccountActivationMailMessage($domain, $urlPath, $logoPath);
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();
        
        return new UserMail(
                $this, $userMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

    public function createResetPasswordMail(string $userMailId): UserMail
    {
        $domain = BaseConfig::KONSULTA_MAIN_URL;
        $urlPath = "/user-account/reset-password/{$this->email}/{$this->resetPasswordCode}";
        $logoPath = BaseConfig::KONSULTA_LOGO_PATH;
        
        $senderMailAddress = BaseConfig::MAIL_SENDER_ADDRESS;
        $senderName = BaseConfig::MAIL_SENDER_NAME;
        $mailMessage = MailMessageBuilder::buildAccountResetPasswordMailMessage($domain, $urlPath, $logoPath);
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();
        
        return new UserMail(
                $this, $userMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
