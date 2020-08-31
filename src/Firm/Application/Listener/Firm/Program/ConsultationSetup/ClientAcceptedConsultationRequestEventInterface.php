<?php

namespace Firm\Application\Listener\Firm\Program\ConsultationSetup;

use Resources\Application\Event\Event;

interface ClientAcceptedConsultationRequestEventInterface extends Event
{

    public function getFirmId(): string;

    public function getClientId(): string;

    public function getProgramId(): string;

    public function getConsultationSessionId(): string;
}
