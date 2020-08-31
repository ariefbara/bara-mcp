<?php

namespace Firm\Application\Listener\Firm\Program\ConsultationSetup;

use Resources\Application\Event\Event;

interface UserAcceptedConsultationRequestEventInterface extends Event
{

    public function getUserId(): string;

    public function getFirmId(): string;

    public function getProgramId(): string;

    public function getConsultationSessionId(): string;
}
