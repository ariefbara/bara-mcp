<?php

namespace Client\Application\Listener;

use Resources\Application\Event\Event;

interface ConsultationRequestNotificationEventInterface extends Event
{

    public function getFirmId(): string;

    public function getPersonnelId(): string;

    public function getConsultantId(): string;

    public function getConsultationRequestId(): string;

    public function getMessageForClient(): string;
}
