<?php

namespace Client\Application\Listener;

use Resources\Application\Event\Event;

interface ConsultantMutateConsultationRequestEventInterface extends Event
{

    public function getFirmId(): string;

    public function getPersonnelId(): string;

    public function getConsultantId(): string;

    public function getConsultationRequestId(): string;

    public function getMessageForClient(): string;
}
