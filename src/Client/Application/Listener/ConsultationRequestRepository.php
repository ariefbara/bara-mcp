<?php

namespace Client\Application\Listener;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfConsultant(
            string $firmId, string $personnelId, string $consultantId, string $consultationRequestId): ConsultationRequest;
}
