<?php

namespace Client\Application\Service\Client\ProgramParticipation\ConsultationSession;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationSession\ConsultationSessionNotification;

interface ConsultationSessionNotificationRepository
{
    public function nextIdentity(): string;
    
    public function add(ConsultationSessionNotification $consultationSessionNotification): void;
}
