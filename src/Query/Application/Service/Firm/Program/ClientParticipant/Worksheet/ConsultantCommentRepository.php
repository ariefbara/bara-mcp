<?php

namespace Query\Application\Service\Firm\Program\ClientParticipant\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\ConsultantComment;

interface ConsultantCommentRepository
{

    public function aConsultantCommentOfClientParticipant(
            string $firmId, string $programId, string $clientId, string $worksheetId, string $consultantCommentId): ConsultantComment;
}
