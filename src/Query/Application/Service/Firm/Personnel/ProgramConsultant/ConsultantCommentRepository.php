<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantComment;

interface ConsultantCommentRepository
{

    public function aCommentFromProgramConsultant(
            ProgramConsultantCompositionId $programConsultantCompositionId, string $consultantCommentId): ConsultantComment;
}
