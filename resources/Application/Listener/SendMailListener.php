<?php

namespace Resources\Application\Listener;

use Resources\Application\ {
    Event\Event,
    Event\Listener,
    Service\SendMail
};

class SendMailListener implements Listener
{

    protected $sendMail;

    public function __construct(SendMail $mailSend)
    {
        $this->sendMail = $mailSend;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    private function execute(CanBeMailedEvent $event)
    {
        $this->sendMail->execute($event->getMail());
    }

}
