<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\Domain\Model\Participant\Worksheet\ConsultantComment;

interface ConsultantCommentRepository
{
    public function aConsultantCommentOfClientParticipant(string $firmId, string $clientId, string $programId, string $worksheetId, string $consultantCommentId): ConsultantComment;
}
