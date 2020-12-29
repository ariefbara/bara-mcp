<?php

namespace Notification\Domain\Model\Firm\Team;

use Notification\Domain\ {
    Model\Firm\Client,
    Model\Firm\Team,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification
};
use SharedContext\Domain\ValueObject\MailMessage;

class Member
{

    /**
     *
     * @var Team
     */
    protected $team;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var bool
     */
    protected $active;

    protected function __construct()
    {
        
    }
    
    public function getClientFullName(): string
    {
        return $this->client->getFullName();
    }

    public function registerClientAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $this->client->registerAsMailRecipient($mailGenerator, $mailMessage, $haltUrlPrepend = true);
    }

    public function registerClientAsNotificationRecipient(ContainNotification $notification): void
    {
        $notification->addClientRecipient($this->client);
    }
    
    public function isActiveMemberNotEqualsTo(?Member $excludedMember): bool
    {
        return isset($excludedMember)? $this->active && $this->id !== $excludedMember->id: $this->active;
    }

}
