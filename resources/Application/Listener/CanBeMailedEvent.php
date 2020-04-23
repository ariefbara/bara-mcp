<?php

namespace Resources\Application\Listener;

use Resources\ {
    Application\Event\Event,
    Domain\Model\Mail
};

interface CanBeMailedEvent extends Event
{
    public function getMail(): Mail;
}
