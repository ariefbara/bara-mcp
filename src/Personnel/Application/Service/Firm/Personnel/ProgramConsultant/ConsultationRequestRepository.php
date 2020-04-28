<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultationRequest;

interface ConsultationRequestRepository
{

//    public function ofId(
//            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultationRequestId): ConsultationRequest;
//
//    public function all(ProgramConsultantCompositionId $programConsultantCompositionId, int $page, int $pageSize): ConsultationRequest;
    
    public function aConsultationRequestById(string $consultationRequestId): ConsultationRequest;
}
