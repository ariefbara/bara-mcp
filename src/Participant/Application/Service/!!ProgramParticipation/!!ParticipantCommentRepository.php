<?php

namespace User\Application\Service\User\ProgramParticipation;

use User\Domain\Model\User\ProgramParticipation\ParticipantComment;

interface ParticipantCommentRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantComment $participantComment): void;

    public function update(): void;

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participantCommentId): ParticipantComment;
}
