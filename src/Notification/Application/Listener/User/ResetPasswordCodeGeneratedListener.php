<?php

namespace Notification\Application\Listener\User;

use Notification\Application\Service\{
    SendImmediateMail,
    User\CreateResetPasswordMail
};
use Resources\{
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ResetPasswordCodeGeneratedListener implements Listener
{

    /**
     *
     * @var CreateResetPasswordMail
     */
    protected $createResetPasswordMail;

    public function __construct(CreateResetPasswordMail $createResetPasswordMail)
    {
        $this->createResetPasswordMail = $createResetPasswordMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(CommonEvent $event): void
    {
        $this->createResetPasswordMail->execute($event->getId());
    }

}
