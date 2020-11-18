<?php

namespace Notification\Domain\Model\Firm;

use DateTimeImmutable;
use Notification\Domain\{
    Model\Firm,
    Model\Firm\Client\ClientMail,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\MailMessage
};
use Resources\Domain\ValueObject\PersonName;

class Client
{

    /**
     *
     * @var Firm
     */
    protected $firm;

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

    public function createActivationMail(string $clientMailId): ClientMail
    {
        $senderMailAddress = $this->firm->getMailSenderAddress();
        $senderName = $this->firm->getMailSenderName();
        $subject = "Konsulta: Aktivasi Akun";
        $message = <<<_MESSAGE
Hi {$this->name->getFirstName()},

Akun konsulta kamu berhasil dibuat, kunjungi tautan berikut untuk melakukan aktivasi:

{$this->firm->getDomain()}/client-account/activate/{$this->email}/{$this->activationCode}/{$this->firm->getIdentifier()}
_MESSAGE;
                
        $htmlMessage = <<<_HTMLMESSAGE
<p> Hi {$this->name->getFirstName()}, </p>

<p>Akun konsulta kamu berhasil dibuat, kunjungi tautan berikut untuk melakukan aktivasi:</p>

<p> <a href="{$this->firm->getDomain()}/client-account/activate/{$this->email}/{$this->activationCode}/{$this->firm->getIdentifier()}">Aktivasi Akun</a></p>
                
_HTMLMESSAGE;

        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();
        
        return new ClientMail(
                $this, $clientMailId, $senderMailAddress, $senderName, $subject, $message, $htmlMessage,
                $recipientMailAddress, $recipientName);
    }

    public function createResetPasswordMail(string $clientMailId): ClientMail
    {
        $senderMailAddress = $this->firm->getMailSenderAddress();
        $senderName = $this->firm->getMailSenderName();
        $subject = "Konsulta: Reset Password";
        $message = <<<_MESSAGE
Hi {$this->name->getFirstName()},

Permintaan reset password akun telah diterima, kunjungi tautan berikut untuk menyelesaikan proses reset password akun:

{$this->firm->getDomain()}/client-account/reset-password/{$this->email}/{$this->activationCode}/{$this->firm->getIdentifier()}

Abaikan email ini jika kamu tidak merasa melakukan permintaan reset password.
_MESSAGE;
        
        $htmlMessage = <<<_HTMLMESSAGE
<p> Hi {$this->name->getFirstName()}, </p>


<p>Permintaan reset password akun telah diterima, kunjungi tautan berikut untuk menyelesaikan proses reset password akun:</p>

<p> <a href="{$this->firm->getDomain()}/client-account/reset-password/{$this->email}/{$this->activationCode}/{$this->firm->getIdentifier()}">reset password</a></p>

<p>Abaikan email ini jika kamu tidak merasa melakukan permintaan reset password.</p>
_HTMLMESSAGE;

        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();
        
        return new ClientMail(
                $this, $clientMailId, $senderMailAddress, $senderName, $subject, $message, $htmlMessage,
                $recipientMailAddress, $recipientName);
    }

}
