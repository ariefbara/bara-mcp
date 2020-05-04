<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Query\Domain\Model\Firm\Program\Participant\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfPersonnel(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationRequestId): ConsultationRequest;

    public function allConsultationRequestsOfPersonnel(
            ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize);
}
