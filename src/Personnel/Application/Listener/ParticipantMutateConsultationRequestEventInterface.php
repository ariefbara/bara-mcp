<?php

namespace Personnel\Application\Listener;

use Resources\Application\Event\Event;

interface ParticipantMutateConsultationRequestEventInterface extends Event
{

    public function getClientId(): string;

    public function getParticipantId(): string;

    public function getConsultationRequestId(): string;

    public function getMessageForPersonnel(): string;
}
