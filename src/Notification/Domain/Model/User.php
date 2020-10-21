<?php

namespace Notification\Domain\Model;

use Config\BaseConfig;
use Notification\Domain\ {
    Model\User\UserMail,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\MailMessage
};
use Resources\Domain\ValueObject\PersonName;

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
        $senderMailAddress = BaseConfig::MAIL_SENDER_ADDRESS;
        $senderName = BaseConfig::MAIL_SENDER_NAME;
        $subject = "Konsulta: Aktivasi Akun";
        $message = <<<_TEXT
Hi {$this->name->getFirstName()},

Akun konsulta kamu berhasil dibuat, kunjungi tautan berikut untuk melakukan aktivasi:

http:konsulta.id/user-account/activate/{$this->email}/{$this->activationCode}
_TEXT;
        
        $htmlMessage = <<<_HTML
<p>Hi {$this->name->getFirstName()},</p>

<p>Akun konsulta kamu berhasil dibuat, kunjungi tautan berikut untuk melakukan aktivasi:</p>

<p><a href="http:konsulta.id/user-account/activate/{$this->email}/{$this->activationCode}">aktivasi akun</a></p>
_HTML;
        
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();
        
        return new UserMail($this, $userMailId, $senderMailAddress, $senderName, $subject, $message, $htmlMessage, $recipientMailAddress, $recipientName);
    }

    public function createResetPasswordMail(string $userMailId): UserMail
    {
        $senderMailAddress = BaseConfig::MAIL_SENDER_ADDRESS;
        $senderName = BaseConfig::MAIL_SENDER_NAME;
        $subject = "Konsulta: Reset Password";
        $message = <<<_TEXT
Hi {$this->name->getFirstName()},

Permintaan reset password akun telah diterima, kunjungi tautan berikut untuk menyelesaikan proses reset password akun:

http:konsulta.id/user-account/reset-password/{$this->email}/{$this->activationCode}

Abaikan email ini jika kamu tidak merasa melakukan permintaan reset password.

_TEXT;
        
        $htmlMessage = <<<_HTML
<p>Hi {$this->name->getFirstName()},</p>

<p>Permintaan reset password akun telah diterima, kunjungi tautan berikut untuk menyelesaikan proses reset password akun:</p>

<p><a href="http:konsulta.id/user-account/reset-password/{$this->email}/{$this->activationCode}">reset password</a></p>

<p>Abaikan email ini jika kamu tidak merasa melakukan permintaan reset password.</p>
_HTML;
        
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();
        
        return new UserMail($this, $userMailId, $senderMailAddress, $senderName, $subject, $message, $htmlMessage, $recipientMailAddress, $recipientName);
    }

}
