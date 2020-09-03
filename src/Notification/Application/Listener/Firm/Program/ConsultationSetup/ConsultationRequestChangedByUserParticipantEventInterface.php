<?php

namespace Notification\Application\Listener\Firm\Program\ConsultationSetup;

use Resources\Application\Event\Event;

interface ConsultationRequestChangedByUserParticipantEventInterface extends Event
{

    public function getUserId(): string;

    public function getProgramParticipationId(): string;

    public function getConsultationRequestId(): string;
}
