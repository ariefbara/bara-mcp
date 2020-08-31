<?php

namespace Firm\Application\Listener\Firm\Program;

use Firm\Application\Service\Firm\Program\SendClientRegistrationAcceptedMail;
use Resources\Application\Event\{
    Event,
    Listener
};

class SendMailWhenClientRegistrationAcceptedListener implements Listener
{

    /**
     *
     * @var SendClientRegistrationAcceptedMail
     */
    protected $sendClientRegistrationAcceptedMail;

    public function __construct(SendClientRegistrationAcceptedMail $sendClientRegistrationAcceptedMail)
    {
        $this->sendClientRegistrationAcceptedMail = $sendClientRegistrationAcceptedMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(ClientRegistrationAcceptedEventInterface $event): void
    {
        $firmId = $event->getFirmId();
        $programId = $event->getProgramId();
        $clientId = $event->getClientId();
        
        $this->sendClientRegistrationAcceptedMail->execute($firmId, $programId, $clientId);
    }

}
