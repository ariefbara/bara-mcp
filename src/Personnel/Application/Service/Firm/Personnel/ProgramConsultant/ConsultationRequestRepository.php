<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

interface ConsultationRequestRepository
{
    
    public function update(): void;

    public function ofId(
            string $firmId, string $personnelId, string $programConsultationId, string $consultationRequestId): ConsultationRequest;
}
