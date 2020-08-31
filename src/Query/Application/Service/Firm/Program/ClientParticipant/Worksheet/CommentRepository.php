<?php

namespace Query\Application\Service\Firm\Program\ClientParticipant\Worksheet;

interface CommentRepository
{
    public function allCommentInClientParticipantWorksheet(string $firmId, string $programId, string $clientId, string $worksheetId, int $page, int $pageSize);
}
