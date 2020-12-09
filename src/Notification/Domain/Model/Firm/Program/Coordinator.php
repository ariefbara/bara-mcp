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
     * @var Personnel
     */
    protected $personnel;

    protected function __construct()
    {
        
    }
    
    public function registerAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $modifiedMail = $mailMessage->prependUrlPath("/coordinators/{$this->id}");
        $this->personnel->registerAsMailRecipient($mailGenerator, $modifiedMail);
    }

    public function registerAsNotificationRecipient(ContainNotification $notification): void
    {
        $notification->addPersonnelRecipient($this->personnel);
    }

}
