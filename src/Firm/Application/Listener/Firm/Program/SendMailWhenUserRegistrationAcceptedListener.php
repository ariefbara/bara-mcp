<?php

namespace Firm\Application\Listener\Firm\Program;

use Firm\Application\Service\Firm\Program\SendUserRegistrationAcceptedMail;
use Resources\Application\Event\ {
    Event,
    Listener
};

class SendMailWhenUserRegistrationAcceptedListener implements Listener
{
    /**
     *
     * @var SendUserRegistrationAcceptedMail
     */
    protected $sendUserRegistrationAcceptedMail;
    
    public function __construct(SendUserRegistrationAcceptedMail $sendUserRegistrationAcceptedMail)
    {
        $this->sendUserRegistrationAcceptedMail = $sendUserRegistrationAcceptedMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(UserRegistrationAcceptedEventInterface $event): void
    {
        $firmId = $event->getFirmId();
        $programId = $event->getProgramId();
        $userId = $event->getUserId();
        
        $this->sendUserRegistrationAcceptedMail->execute($firmId, $programId, $userId);
    }

}
