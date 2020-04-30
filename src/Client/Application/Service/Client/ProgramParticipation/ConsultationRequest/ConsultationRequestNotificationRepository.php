<?php

namespace Client\Application\Service\Client\ProgramParticipation\ConsultationRequest;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationRequest\ConsultationRequestNotification;

interface ConsultationRequestNotificationRepository
{
    public function nextIdentity(): string;
    
    public function add(ConsultationRequestNotification $consultationRequestNotification): void;
}
