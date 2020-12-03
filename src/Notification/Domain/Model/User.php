<?php

namespace Notification\Domain\Model;

use Config\BaseConfig;
use DateTimeImmutable;
use Notification\Domain\ {
    Model\User\UserMail,
    SharedModel\CanSendPersonalizeMail
};
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\MailMessage;

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
        $mailMessage = $mailMessage->appendRecipientFirstNameInGreetings($this->name->getFirstName());

        $mailGenerator->addMail($mailMessage, $this->email, $this->name->getFullName());
    }

    public function createActivationMail(string $userMailId): UserMail
    {
        $subject = "Aktivasi Akun";
        $greetings = "Hi {$this->name->getFirstName()}";
        $mainMessage = "Akun konsulta kamu berhasil dibuat, kunjungi tautan berikut untuk melakukan aktivasi:";
        $domain = "http://konsulta.id";
        $urlPath = "/user-account/activate/{$this->email}/{$this->activationCode}";
        $logoPath = BaseConfig::KONSULTA_LOGO_PATH;
        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        
        $senderMailAddress = BaseConfig::MAIL_SENDER_ADDRESS;
        $senderName = BaseConfig::MAIL_SENDER_NAME;
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();
        
        return new UserMail(
                $this, $userMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

    public function createResetPasswordMail(string $userMailId): UserMail
    {
        $subject = "Reset Password";
        $greetings = "Hi {$this->name->getFirstName()}";
        $mainMessage = "Permintaan reset password akun telah diterima, kunjungi tautan berikut untuk menyelesaikan proses reset password akun:";
        $domain = "http://konsulta.id";
        $urlPath = "/user-account/reset-password/{$this->email}/{$this->resetPasswordCode}";
        $logoPath = BaseConfig::KONSULTA_LOGO_PATH;
        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        
        $senderMailAddress = BaseConfig::MAIL_SENDER_ADDRESS;
        $senderName = BaseConfig::MAIL_SENDER_NAME;
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();
        
        return new UserMail(
                $this, $userMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress, $recipientName);
    }

}
