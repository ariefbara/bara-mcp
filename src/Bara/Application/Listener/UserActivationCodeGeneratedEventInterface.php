<?php

namespace Bara\Application\Listener;

use Resources\Application\Event\Event;

interface UserActivationCodeGeneratedEventInterface extends Event
{
    public function getUserId(): string;
}
