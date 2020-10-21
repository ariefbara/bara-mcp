<?php

namespace Notification\Application\Listener\Client;

use Notification\Application\Service\ {
    Client\CreateClientResetPasswordMail,
    SendImmediateMail
};
use Resources\ {
    Application\Event\Event,
    Application\Event\Listener,
    Domain\Event\CommonEvent
};

class ResetPasswordCodeGeneratedListener implements Listener
{

    /**
     *
     * @var CreateClientResetPasswordMail
     */
    protected $createClientResetPasswordMail;

    /**
     *
     * @var SendImmediateMail
     */
    protected $sendImmediateMail;

    public function __construct(
            CreateClientResetPasswordMail $createClientResetPasswordMail, SendImmediateMail $sendImmediateMail)
    {
        $this->createClientResetPasswordMail = $createClientResetPasswordMail;
        $this->sendImmediateMail = $sendImmediateMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
        $this->sendImmediateMail->execute();
    }
    
    public function execute(CommonEvent $event): void
    {
        $this->createClientResetPasswordMail->execute($event->getId());
    }

}
