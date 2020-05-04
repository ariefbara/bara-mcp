<?php

namespace Personnel\Application\Listener;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfParticipant(
            string $clientId, string $participantId, string $consultationRequestId): ConsultationRequest;
}
