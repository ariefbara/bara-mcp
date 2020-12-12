<?php

namespace Notification\Domain\Model\Firm;

use DateTimeImmutable;
use Notification\Domain\Model\Firm;
use Notification\Domain\Model\Firm\Manager\ManagerMail;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationForAllUser;
use SharedContext\Domain\ValueObject\MailMessage;

class Manager
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
     * @var string
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

    public function createResetPasswordMail(string $managerMailId): ManagerMail
    {
        $subject = "Konsulta: reset password";
        $greetings = "Hi $this->name";
        $mainMessage = <<<_MESSAGE
Permintaan reset password akun telah kami terima, kunjungi tautan di bawah untuk menyelesaikan proses reset password.
_MESSAGE;
        $domain = $this->firm->getDomain();
        $urlPath = "/manager-account/reset-password/{$this->email}/{$this->resetPasswordCode}/{$this->firm->getIdentifier()}";
        $logoPath = $this->firm->getLogoPath();
        
        $mailMessage = new MailMessage($subject, $greetings, $mainMessage, $domain, $urlPath, $logoPath);

        $senderMailAddress = $this->firm->getMailSenderAddress();
        $senderName = $this->firm->getMailSenderName();
        $recipientMailAddress = $this->email;
        $recipientName = $this->name;

        return new ManagerMail(
                $this, $managerMailId, $senderMailAddress, $senderName, $mailMessage, $recipientMailAddress,
                $recipientName);
    }
    
    public function registerAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $modifiedMail = $mailMessage->appendRecipientFirstNameInGreetings($this->name)
                ->prependUrlPath("/manager");
        $mailGenerator->addMail($modifiedMail, $this->email, $this->name);
    }
    
}
