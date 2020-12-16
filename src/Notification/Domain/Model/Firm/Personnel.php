<?php

namespace Notification\Domain\Model\Firm;

use DateTimeImmutable;
use Notification\Domain\Model\Firm;
use Notification\Domain\Model\Firm\Personnel\PersonnelMail;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Resources\Domain\ValueObject\PersonName;
use SharedContext\Domain\ValueObject\MailMessage;
use SharedContext\Domain\ValueObject\MailMessageBuilder;

class Personnel
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
        $modifiedMailMessage = $mailMessage->appendRecipientFirstNameInGreetings($this->name->getFirstName());

        $mailGenerator->addMail($modifiedMailMessage, $this->email, $this->name->getFullName());
    }

    public function createResetPasswordMail(string $personnelMailId): PersonnelMail
    {
        $domain = $this->firm->getDomain();
        $urlPath = "/personnel-account/reset-password/{$this->email}/{$this->resetPasswordCode}/{$this->firm->getIdentifier()}";
        $logoPath = $this->firm->getLogoPath();
        
        $senderMailAddress = $this->firm->getMailSenderAddress();
        $senderName = $this->firm->getMailSenderName();
        $mailMessage = MailMessageBuilder::buildAccountResetPasswordMailMessage($domain, $urlPath, $logoPath);
        $recipientMailAddress = $this->email;
        $recipientName = $this->getFullName();
        
        return new PersonnelMail(
                $this, $personnelMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress,
                $recipientName);
    }

}
