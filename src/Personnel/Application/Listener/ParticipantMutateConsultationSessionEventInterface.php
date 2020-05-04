<?php

namespace Personnel\Application\Listener;

use Resources\Application\Event\Event;

interface ParticipantMutateConsultationSessionEventInterface extends Event
{
    public function getClientId(): string;

    public function getParticipantId(): string;

    public function getConsultationSessionId(): string;

    public function getMessageForPersonnel(): string;
}
