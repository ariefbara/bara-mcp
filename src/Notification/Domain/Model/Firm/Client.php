<?php

namespace Notification\Domain\Model\Firm;

use DateTimeImmutable;
use Notification\Domain\Model\Firm;
use Notification\Domain\Model\Firm\Client\ClientMail;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\MailMessage;
use SharedContext\Domain\ValueObject\MailMessageBuilder;

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

    public function registerAsMailRecipient(
            CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage, ?bool $haltUrlPrepend = false): void
    {
        $modifiedMailMessage = $mailMessage->appendRecipientFirstNameInGreetings($this->name->getFirstName());
        if (!$haltUrlPrepend) {
            $modifiedMailMessage = $modifiedMailMessage->prependUrlPath("/client");
        }
        $mailGenerator->addMail($modifiedMailMessage, $this->email, $this->name->getFullName());
    }

    public function createActivationMail(string $clientMailId): ClientMail
    {
        $domain = $this->firm->getDomain();
        $urlPath = "/client-account/activate/{$this->email}/{$this->activationCode}/{$this->firm->getIdentifier()}";
        $logoPath = $this->firm->getLogoPath();
        
        $mailMessage = MailMessageBuilder::buildAccountActivationMailMessage($domain, $urlPath, $logoPath)
                ->appendRecipientFirstNameInGreetings($this->name->getFirstName());
        $senderMailAddress = $this->firm->getMailSenderAddress();
        $senderName = $this->firm->getMailSenderName();
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();

        return new ClientMail(
                $this, $clientMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress,
                $recipientName);
    }

    public function createResetPasswordMail(string $clientMailId): ClientMail
    {
        $domain = $this->firm->getDomain();
        $urlPath = "/client-account/reset-password/{$this->email}/{$this->resetPasswordCode}/{$this->firm->getIdentifier()}";
        $logoPath = $this->firm->getLogoPath();
        
        $mailMessage = MailMessageBuilder::buildAccountResetPasswordMailMessage($domain, $urlPath, $logoPath)
                ->appendRecipientFirstNameInGreetings($this->name->getFirstName());
        $senderMailAddress = $this->firm->getMailSenderAddress();
        $senderName = $this->firm->getMailSenderName();
        $recipientMailAddress = $this->email;
        $recipientName = $this->name->getFullName();

        return new ClientMail(
                $this, $clientMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress,
                $recipientName);
    }

}
