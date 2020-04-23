<?php
namespace Resources\Application\Event;

interface Listener
{
    public function handle(Event $event): void;
}

