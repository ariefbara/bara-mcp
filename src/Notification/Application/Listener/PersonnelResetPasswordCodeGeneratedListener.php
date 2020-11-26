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

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    function __construct(CreatePersonnelResetPasswordMail $createPersonnelResetPasswordMail,
            SendImmediateMail $sendImmediateMail)
    {
        $this->createPersonnelResetPasswordMail = $createPersonnelResetPasswordMail;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    protected function execute(CommonEvent $event): void
    {
        $this->createPersonnelResetPasswordMail->execute($event->getId());
    }

}
