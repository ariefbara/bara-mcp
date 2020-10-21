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

    protected function execute(CommonEvent $event): void
    {
        $this->createActivationMail->execute($event->getId());
    }

}
