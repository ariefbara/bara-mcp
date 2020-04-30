<?php

namespace Client\Application\Listener;

use Resources\Application\Event\Event;

interface ConsultationSessionNotificationEventInterface extends Event
{

    public function getFirmId(): string;

    public function getPersonnelId(): string;

    public function getConsultantId(): string;

    public function getConsultationSessionId(): string;

    public function getmessageForClient(): string;
}
