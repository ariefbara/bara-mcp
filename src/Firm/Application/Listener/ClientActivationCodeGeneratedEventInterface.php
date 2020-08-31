<?php

namespace Firm\Application\Listener;

use Resources\Application\Event\Event;

interface ClientActivationCodeGeneratedEventInterface extends Event
{

    public function getFirmId(): string;

    public function getClientId(): string;
}
