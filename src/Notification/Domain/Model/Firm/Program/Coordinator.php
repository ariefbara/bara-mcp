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
            CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage, ?bool $haltPrependUrlPath): void
    {
        if (!$haltPrependUrlPath) {
            $mailMessage = $mailMessage->prependUrlPath("/coordinators/{$this->id}");
        }
        $this->personnel->registerAsMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerAsNotificationRecipient(ContainNotification $notification): void
    {
        $notification->addPersonnelRecipient($this->personnel);
    }

}
