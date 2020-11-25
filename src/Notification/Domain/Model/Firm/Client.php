<?php

namespace Notification\Domain\Model\Firm;

use DateTimeImmutable;
use Notification\Domain\{
    Model\Firm,
    Model\Firm\Client\ClientMail,
    SharedModel\CanSendPersonalizeMail
};
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\MailMessage;

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
        $greetings = "Hi {$this->name->getFirstName()}";
        $mainMessage = "Akun konsulta kamu berhasil dibuat, kunjungi tautan berikut untuk melakukan aktivasi:";
        $domain = $this->firm->getDomain();
        $urlPath = "/client-account/activate/{$this->email}/{$this->activationCode}/{$this->firm->getIdentifier()}";
        $logoPath = $this->firm->getLogoPath();

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();

        return new ClientMail(
                $this, $clientMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress,
                $recipientName);
    }

    public function createResetPasswordMail(string $clientMailId): ClientMail
    {
        $senderMailAddress = $this->firm->getMailSenderAddress();
        $senderName = $this->firm->getMailSenderName();
        $subject = "Konsulta: Reset Password";
        $greetings = "Hi {$this->name->getFirstName()}";
        $mainMessage = "Permintaan reset password akun telah diterima, kunjungi tautan berikut untuk menyelesaikan proses reset password akun:";
        $domain = $this->firm->getDomain();
        $urlPath = "/client-account/reset-password/{$this->email}/{$this->resetPasswordCode}/{$this->firm->getIdentifier()}";
        $logoPath = $this->firm->getLogoPath();

        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();

        return new ClientMail(
                $this, $clientMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress,
                $recipientName);
    }

}
