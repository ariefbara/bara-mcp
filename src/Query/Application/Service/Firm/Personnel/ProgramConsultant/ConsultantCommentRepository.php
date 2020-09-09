<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantComment;

interface ConsultantCommentRepository
{
    public function ofId(string $firmId, string $personnelId, string $programConsultationId, string $consultantCommentId): ConsultantComment;
}
