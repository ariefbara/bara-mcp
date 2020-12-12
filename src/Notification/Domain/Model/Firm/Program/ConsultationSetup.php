<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Program;
use Notification\Domain\SharedModel\CanSendPersonalizeMail;
use Notification\Domain\SharedModel\ContainNotificationforCoordinator;
use SharedContext\Domain\ValueObject\MailMessage;

class ConsultationSetup
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

    protected function __construct()
    {
        
    }

    public function registerAllCoordinatorsAsMailRecipient(
            CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $this->program->registerAllCoordinatorsAsMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerAllCoordinatorsAsNotificationRecipient(ContainNotificationforCoordinator $notification): void
    {
        $this->program->registerAllCoordiantorsAsNotificationRecipient($notification);
    }

}
