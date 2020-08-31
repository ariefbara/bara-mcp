<?php

namespace Firm\Application\Service\Firm\Program\ConsultationSetup;

use Firm\Domain\Model\Firm\Program\ConsultationSetup\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfClient(
            string $firmId, string $clientId, string $programId, string $consultationRequestId): ConsultationRequest;
}
