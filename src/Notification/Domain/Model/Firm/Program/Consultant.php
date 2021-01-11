<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;

class Consultant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    protected function __construct()
    {
        
    }

    public function getPersonnelFullName(): string
    {
        return $this->personnel->getFullName();
    }

    public function registerMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage, ?bool $haltPrependUrl = false): void
    {
        $mailMessage  = $mailMessage->appendRecipientFirstNameInGreetings("mentor");
        if (!$haltPrependUrl) {
            $mailMessage = $mailMessage->prependUrlPath("/program-consultant/{$this->id}/program/{$this->program->getId()}");
        }
        $this->personnel->registerAsMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerNotificationRecipient(ContainNotification $notification): void
    {
        $notification->addPersonnelRecipient($this->personnel);
    }
    
    public function registerAsCommentMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $mailMessage = $mailMessage->prependUrlPath("/as-consultant/{$this->id}/program/{$this->program->getId()}")
                ->appendRecipientFirstNameInGreetings("mentor");
        $this->personnel->registerAsMailRecipient($mailGenerator, $mailMessage);
    }
    
    public function getProgramId(): string
    {
        return $this->program->getId();
    }

}
