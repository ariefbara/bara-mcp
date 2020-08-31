<?php

namespace Bara\Application\Listener;

use Bara\Application\Service\SendUserResetPasswordCodeMail;
use Resources\Application\Event\{
    Event,
    Listener
};

class SendMailWhenUserResetPasswordCodeGeneratedListener implements Listener
{

    /**
     *
     * @var SendUserResetPasswordCodeMail
     */
    protected $sendUserResetPasswordCodeMail;

    public function __construct(SendUserResetPasswordCodeMail $sendUserResetPasswordCodeMail)
    {
        $this->sendUserResetPasswordCodeMail = $sendUserResetPasswordCodeMail;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(UserResetPasswordCodeGeneratedEventInterface $event): void
    {
        $this->sendUserResetPasswordCodeMail->execute($event->getUserId());
    }

}
