<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

interface ParticipantCommentRepository
{
    public function aClientParticipantComment(string $firmId, string $clientId, string $programId, string $worksheetId, string $participantCommentId): ParticipantComment;
}
