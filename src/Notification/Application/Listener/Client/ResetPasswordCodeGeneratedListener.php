<?php

namespace Notification\Application\Listener\Client;

use Notification\Application\Service\ {
    Client\CreateClientResetPasswordMail,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ResetPasswordCodeGeneratedListener implements Listener
{

    /**
     *
     * @var CreateClientResetPasswordMail
     */
    protected $createClientResetPasswordMail;

    public function __construct(
            CreateClientResetPasswordMail $createClientResetPasswordMail)
    {
        $this->createClientResetPasswordMail = $createClientResetPasswordMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    public function execute(CommonEvent $event): void
    {
        $this->createClientResetPasswordMail->execute($event->getId());
    }

}
