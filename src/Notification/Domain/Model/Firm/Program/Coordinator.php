<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Personnel;
use Notification\Domain\Model\Firm\Program;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotification;
use SharedContext\Domain\ValueObject\MailMessage;

class Coordinator
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
     * @var bool
     */
    protected $active;

    /**
     *
     * @var Personnel
     */
    protected $personnel;

    function isActive(): bool
    {
        return $this->active;
    }

    protected function __construct()
    {
        
    }

    public function registerAsMailRecipient(
            CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage, ?bool $haltPrependUrlPath = false): void
    {
        $mailMessage = $mailMessage->appendRecipientFirstNameInGreetings("coordinator");
        if (!$haltPrependUrlPath) {
            $mailMessage = $mailMessage->prependUrlPath("/as-coordinator/{$this->id}/program/{$this->program->getId()}");
        }
        $this->personnel->registerAsMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerAsNotificationRecipient(ContainNotification $notification): void
    {
        $notification->addPersonnelRecipient($this->personnel);
    }
    
    public function getProgramId(): string
    {
        return $this->program->getId();
    }

}
