<?php

namespace Notification\Application\Listener\Client;

use Notification\Application\Service\ {
    Client\CreateActivationMail,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ActivationCodeGeneratedListener implements Listener
{

    /**
     *
     * @var CreateActivationMail
     */
    protected $createActivationMail;

    public function __construct(CreateActivationMail $createActivationMail)
    {
        $this->createActivationMail = $createActivationMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    public function execute(CommonEvent $event): void
    {
        $this->createActivationMail->execute($event->getId());
    }

}
