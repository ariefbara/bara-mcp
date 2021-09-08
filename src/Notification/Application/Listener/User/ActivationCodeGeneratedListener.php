<?php

namespace Notification\Application\Listener\User;

use Notification\Application\Service\{
    SendImmediateMail,
    User\CreateActivationMail
};
use Resources\{
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

    protected function execute(CommonEvent $event): void
    {
        $this->createActivationMail->execute($event->getId());
    }

}
