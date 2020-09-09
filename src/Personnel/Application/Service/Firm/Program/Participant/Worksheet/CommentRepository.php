<?php

namespace Personnel\Application\Service\Firm\Program\Participant\Worksheet;

use Personnel\Domain\Model\Firm\Program\Participant\Worksheet\Comment;

interface CommentRepository
{

    public function aCommentInProgramWorksheetWhereConsultantInvolved(
            string $firmId, string $personnelId, string $programConsultationId, string $participantId,
            string $worksheetId, string $commentId): Comment;
}
