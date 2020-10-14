<?php

namespace Participant\Application\Listener;

use Resources\Application\Event\Event;

interface EventTriggeredByParticipantInterface extends Event
{

    public function getParticipantId(): string;

    public function getId(): string;
}
