<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\ {
    Model\Firm\Personnel,
    Model\Firm\Program,
    SharedModel\canSendPersonalizeMail,
    SharedModel\ContainNotification
};
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

    public function registerMailRecipient(canSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $modifiedMailMessage = $mailMessage->prependUrlPath("/program-consultations/{$this->id}");
        $this->personnel->registerAsMailRecipient($mailGenerator, $modifiedMailMessage);
    }

    public function registerNotificationRecipient(ContainNotification $notification): void
    {
        $notification->addPersonnelRecipient($this->personnel);
    }

}
