<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ParticipantComment;

interface ParticipantCommentRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantComment $participantComment): void;

    public function update(): void;

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participantCommentId): ParticipantComment;
}
