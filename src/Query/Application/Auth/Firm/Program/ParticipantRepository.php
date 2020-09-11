<?php

namespace Query\Application\Auth\Firm\Program;

interface ParticipantRepository
{

    public function containRecordOfActiveParticipantCorrespondWithClient(
            string $firmId, string $programId, string $clientId): bool;

    public function containRecordOfActiveParticipantCorrespondWithUser(string $firmId, string $programId, string $userId): bool;
}
