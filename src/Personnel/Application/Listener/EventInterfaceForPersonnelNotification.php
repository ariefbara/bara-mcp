<?php

namespace Personnel\Application\Listener;

use Resources\Application\Event\Event;

interface EventInterfaceForPersonnelNotification extends Event
{

    public function getId(): string;

    public function getMessageForPersonnel(): string;
}
