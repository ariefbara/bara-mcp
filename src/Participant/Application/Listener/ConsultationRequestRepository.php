<?php

namespace User\Application\Listener;

use User\Domain\Model\User\ProgramParticipation\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfConsultant(
            string $firmId, string $personnelId, string $consultantId, string $consultationRequestId): ConsultationRequest;
}
