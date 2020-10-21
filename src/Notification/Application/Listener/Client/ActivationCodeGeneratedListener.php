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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;
    
    public function __construct(CreateActivationMail $createActivationMail, SendImmediateMail $sendImmediateMail)
    {
        $this->createActivationMail = $createActivationMail;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    public function execute(CommonEvent $event): void
    {
        $this->createActivationMail->execute($event->getId());
    }

}
