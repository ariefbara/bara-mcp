<?php

namespace User\Application\Listener;

use Resources\Application\Event\ {
    Event,
    Listener
};

class UserRegisteredListener implements Listener
{

    /**
     *
     * @var string
     */
    protected $userRegistrantId;

    public function getUserRegistrantId(): string
    {
        return $this->userRegistrantId;
    }

    public function __construct()
    {
        ;
    }

    public function handle(Event $event): void
    {
        $this->execute($event);
    }

    protected function execute(UserRegisteredEventInterface $event): void
    {
        $this->userRegistrantId = $event->getUserRegistrantId();
    }

}
