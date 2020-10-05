<?php

namespace Notification\Application\Listener;

use Resources\Application\Event\Event;

interface NotificationEvent extends Event
{
    public function getId(): string;
}
