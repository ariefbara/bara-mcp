<?php

namespace Personnel\Application\Service\Firm\Program\Participant\Worksheet;

use Personnel\Domain\Model\Firm\Program\Participant\Worksheet\Comment;
use Query\Application\Service\Firm\Personnel\PersonnelCompositionId;

interface CommentRepository
{

    public function aCommentInProgramWorksheetWhereConsultantInvolved(
            PersonnelCompositionId $personnelCompositionId, string $programConsultantId, string $participantId,
            string $worksheetId, string $commentId): Comment;
}
