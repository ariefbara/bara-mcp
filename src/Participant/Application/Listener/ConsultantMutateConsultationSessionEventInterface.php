<?php

namespace User\Application\Listener;

use Resources\Application\Event\Event;

interface ConsultantMutateConsultationSessionEventInterface extends Event
{

    public function getFirmId(): string;

    public function getPersonnelId(): string;

    public function getConsultantId(): string;

    public function getConsultationSessionId(): string;

    public function getmessageForUser(): string;
}
