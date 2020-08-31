<?php

namespace Firm\Application\Listener\Firm\Program;

use Resources\Application\Event\Event;

interface ClientRegistrationAcceptedEventInterface extends Event
{

    public function getFirmId(): string;

    public function getProgramId(): string;

    public function getClientId(): string;
}
