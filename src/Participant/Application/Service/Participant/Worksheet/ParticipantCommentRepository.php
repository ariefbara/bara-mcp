<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\Domain\Model\Participant\Worksheet\ParticipantComment;

interface ParticipantCommentRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantComment $participantComment): void;

    public function aParticipantCommentOfClientParticipant(
            string $firmId, string $clientId, string $programId, string $worksheetId, string $participantCommentId): ParticipantComment;

    public function update(): void;
}
