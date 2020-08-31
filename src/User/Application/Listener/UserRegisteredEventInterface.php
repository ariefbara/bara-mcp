<?php

namespace User\Application\Listener;

use Resources\Application\Event\Event;

interface UserRegisteredEventInterface extends Event
{

    public function getUserRegistrantId(): string;
}
