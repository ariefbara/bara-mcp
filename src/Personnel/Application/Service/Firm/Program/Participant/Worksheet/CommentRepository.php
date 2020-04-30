<?php

namespace Personnel\Application\Service\Firm\Program\Participant\Worksheet;

use Personnel\{
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};

interface CommentRepository
{

    public function aCommentInProgramWorksheetWhereConsultantInvolved(
            PersonnelCompositionId $personnelCompositionId, string $programConsultantId, string $participantId,
            string $worksheetId, string $commentId): Comment;
}
