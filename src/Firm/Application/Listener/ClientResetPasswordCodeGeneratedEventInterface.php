<?php

namespace Firm\Application\Listener;

use Resources\Application\Event\Event;

interface ClientResetPasswordCodeGeneratedEventInterface extends Event
{

    public function getFirmId(): string;

    public function getClientId(): string;
}
