<?php

namespace Notification\Domain\Model\Firm\Client;

use Notification\Domain\Model\SharedEntity\KonsultaMailMessage;
use Resources\Application\Service\{
    Mailer,
    SenderInterface
};

class ClientMailNotification
{

    /**
     *
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send(Mailer $mailer, SenderInterface $sender, KonsultaMailMessage $mailMessage): void
    {
        $mailMessage->appendRecipientFirstNameInGreetings($this->client->getFirstName());
        $mailMessage->prependApiPath("/client");
        $mailer->send($sender, $mailMessage, $recipient);
    }

}
