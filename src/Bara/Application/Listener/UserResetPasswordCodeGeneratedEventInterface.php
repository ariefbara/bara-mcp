<?php

namespace Bara\Application\Listener;

use Resources\Application\Event\Event;

interface UserResetPasswordCodeGeneratedEventInterface extends Event
{
    public function getUserId(): string;
}
