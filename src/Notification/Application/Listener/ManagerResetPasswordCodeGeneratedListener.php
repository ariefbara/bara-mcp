<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    CreateManagerResetPasswordMail,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ManagerResetPasswordCodeGeneratedListener implements Listener
{

    /**
     *
     * @var CreateManagerResetPasswordMail
     */
    protected $createManagerResetPasswordMail;

    function __construct(CreateManagerResetPasswordMail $createManagerResetPasswordMail)
    {
        $this->createManagerResetPasswordMail = $createManagerResetPasswordMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $managerId = $event->getId();
        $this->createManagerResetPasswordMail->execute($managerId);
    }

}
