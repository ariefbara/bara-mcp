<?php

namespace Bara\Application\Listener;

use Bara\Application\Service\SendUserActivationCodeMail;
use Resources\Application\Event\{
    Event,
    Listener
};

class SendMailWhenUserActivationCodeGeneratedListener implements Listener
{

    /**
     *
     * @var SendUserActivationCodeMail
     */
    protected $sendUserActivationCodeMail;

    public function __construct(SendUserActivationCodeMail $sendUserActivationCodeMail)
    {
        $this->sendUserActivationCodeMail = $sendUserActivationCodeMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(UserActivationCodeGeneratedEventInterface $event): void
    {
        $this->sendUserActivationCodeMail->execute($event->getUserId());
    }

}
