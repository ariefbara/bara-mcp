<?php

namespace Client\Application\Listener;

use Resources\Application\Event\Event;

interface ParticipantNotificationEventInterface extends Event
{

    public function getFirmId(): string;

    public function getProgramId(): string;

    public function getParticipantId(): string;

    public function getMessageForClient(): string;
}
