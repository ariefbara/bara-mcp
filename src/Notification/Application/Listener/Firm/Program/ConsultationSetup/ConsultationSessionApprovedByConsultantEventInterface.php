<?php

namespace Notification\Application\Listener\Firm\Program\ConsultationSetup;

use Resources\Application\Event\Event;

interface ConsultationSessionApprovedByConsultantEventInterface extends Event
{

    public function getFirmId(): string;

    public function getPersonnelId(): string;

    public function getProgramConsultationId(): string;

    public function getConsultationSessionId(): string;
}
