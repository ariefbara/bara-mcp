<?php

namespace Query\Application\Auth\Firm;

interface ParticipantRepository
{
    public function containRecordOfParticipantInFirmCorrespondWithUser(string $firmId, string $userId): bool;
}
