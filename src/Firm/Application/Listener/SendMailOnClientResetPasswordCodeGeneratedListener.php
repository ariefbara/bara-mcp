<?php

namespace Firm\Application\Listener;

use Firm\Application\Service\Firm\SendClientResetPasswordCodeMail;
use Resources\Application\Event\ {
    Event,
    Listener
};

class SendMailOnClientResetPasswordCodeGeneratedListener implements Listener
{
    /**
     *
     * @var SendClientResetPasswordCodeMail
     */
    protected $sendClientResetPasswordCodeMail;
    
    public function __construct(SendClientResetPasswordCodeMail $sendClientResetPasswordCodeMail)
    {
        $this->sendClientResetPasswordCodeMail = $sendClientResetPasswordCodeMail;
    }
    
    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(ClientResetPasswordCodeGeneratedEventInterface $event): void
    {
        $firmId = $event->getFirmId();
        $clientId = $event->getClientId();
        
        $this->sendClientResetPasswordCodeMail->execute($firmId, $clientId);
    }

}
