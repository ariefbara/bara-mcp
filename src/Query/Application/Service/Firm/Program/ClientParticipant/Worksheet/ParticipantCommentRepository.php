<?php

namespace Query\Application\Service\Firm\Program\ClientParticipant\Worksheet;

use Query\Domain\Model\Firm\Program\Participant\Worksheet\ParticipantComment;

interface ParticipantCommentRepository
{

    public function aParticipantCommentOfClientParticipant(
            string $firmId, string $programId, string $clientId, string $worksheetId, string $participantCommentId): ParticipantComment;
}
