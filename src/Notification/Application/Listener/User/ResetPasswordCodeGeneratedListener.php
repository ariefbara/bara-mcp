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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(CreateResetPasswordMail $createResetPasswordMail, SendImmediateMail $sendImmediateMail)
    {
        $this->createResetPasswordMail = $createResetPasswordMail;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }

    protected function execute(CommonEvent $event): void
    {
        $this->createResetPasswordMail->execute($event->getId());
    }

}
