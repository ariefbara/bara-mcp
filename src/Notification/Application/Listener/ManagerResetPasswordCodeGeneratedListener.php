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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    function __construct(CreateManagerResetPasswordMail $createManagerResetPasswordMail,
            SendImmediateMail $sendImmediateMail)
    {
        $this->createManagerResetPasswordMail = $createManagerResetPasswordMail;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(CommonEvent $event): void
    {
        $managerId = $event->getId();
        $this->createManagerResetPasswordMail->execute($managerId);
    }

}
