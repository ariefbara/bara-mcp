<?php

namespace Notification\Application\Listener;

use Notification\Application\Service\ {
    CreatePersonnelResetPasswordMail,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class PersonnelResetPasswordCodeGeneratedListener implements Listener
{

    /**
     *
     * @var CreatePersonnelResetPasswordMail
     */
    protected $createPersonnelResetPasswordMail;

    function __construct(CreatePersonnelResetPasswordMail $createPersonnelResetPasswordMail)
    {
        $this->createPersonnelResetPasswordMail = $createPersonnelResetPasswordMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }
    
    protected function execute(CommonEvent $event): void
    {
        $this->createPersonnelResetPasswordMail->execute($event->getId());
    }

}
