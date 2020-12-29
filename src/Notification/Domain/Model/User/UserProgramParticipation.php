<?php

namespace Notification\Domain\Model\User;

use Notification\Domain\ {
    Model\Firm\Program\Participant,
    Model\User,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;

class UserProgramParticipation
{

    /**
     *
     * @var User
     */
    protected $user;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Participant
     */
    protected $programParticipation;
    
    protected function __construct()
    {
    }
    
    public function getUserFullName(): string
    {
        return $this->user->getFullName();
    }
    
    public function registerUserAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $modifiedMailMessage = $mailMessage->prependUrlPath("/participation/{$this->id}");
        $this->user->registerAsMailRecipient($mailGenerator, $modifiedMailMessage);
    }
    
    public function registerUserAsNotificationRecipient(ContainNotification $notification): void
    {
        $notification->addUserRecipient($this->user);
    }

}
