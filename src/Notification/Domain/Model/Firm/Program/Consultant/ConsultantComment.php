<?php

namespace Notification\Domain\Model\Firm\Program\Consultant;

use Notification\Domain\ {
    Model\Firm\Program\Consultant,
    Model\Firm\Program\Participant\Worksheet\Comment,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;

class ConsultantComment
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Comment
     */
    protected $comment;

    protected function __construct()
    {
        
    }

    public function registerConsultantAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $this->consultant->registerAsCommentMailRecipient($mailGenerator, $mailMessage);
    }

    public function registerConsultantAsNotificationRecipient(ContainNotification $notification): void
    {
        $this->consultant->registerNotificationRecipient($notification);
    }

    public function getConsultantName(): string
    {
        return $this->consultant->getPersonnelFullName();
    }

}
