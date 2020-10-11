<?php

namespace Notification\Domain\Model\Firm\Client;

use Notification\Domain\ {
    Model\Firm\Client,
    Model\Firm\Program\Participant,
    SharedModel\CanSendPersonalizeMail,
    SharedModel\ContainNotification,
    SharedModel\MailMessage
};

class ClientProgramParticipation
{

    /**
     *
     * @var Client
     */
    protected $client;

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
    
    public function getClientFullName(): string
    {
        return $this->client->getFullName();
    }
    
    public function registerClientAsMailRecipient(CanSendPersonalizeMail $mailGenerator, MailMessage $mailMessage): void
    {
        $modifiedMailMessage = $mailMessage->prependUrlPath("/program-participations/{$this->id}");
        $this->client->registerAsMailRecipient($mailGenerator, $modifiedMailMessage);
    }
    
    public function registerClientAsNotificationRecipient(ContainNotification $notification): void
    {
        $notification->addClientRecipient($this->client);
    }

}
