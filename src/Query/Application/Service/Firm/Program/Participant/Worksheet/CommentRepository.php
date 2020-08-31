<?php

namespace Query\Application\Service\Firm\Program\Participant\Worksheet;

use Query\ {
    Application\Service\Firm\Client\ProgramParticipation\Worksheet\CommentRepository as InterfaceForClient,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};

interface CommentRepository extends InterfaceForClient
{

    public function ofId(
            string $firmId, string $programId, string $participantId, string $worksheetId, string $commentId): Comment;

    public function all(
            string $firmId, string $programId, string $participantId, string $worksheetId, int $page, int $pageSize);
}
