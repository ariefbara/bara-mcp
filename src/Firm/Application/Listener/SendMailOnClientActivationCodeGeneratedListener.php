<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\Firm\SendClientActivationCodeMail;
use Resources\Application\Event\ {
    Event,
    Listener
};

class SendMailOnClientActivationCodeGeneratedListener implements Listener
{

    /**
     *
     * @var SendClientActivationCodeMail
     */
    protected $sendClientActivationCodeMail;

    public function __construct(SendClientActivationCodeMail $sendClientActivationCodeMail)
    {
        $this->sendClientActivationCodeMail = $sendClientActivationCodeMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(ClientActivationCodeGeneratedEventInterface $event): void
    {
        $firmId = $event->getFirmId();
        $clientId = $event->getClientId();
        $this->sendClientActivationCodeMail->execute($firmId, $clientId);
    }

}
