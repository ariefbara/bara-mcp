<?php

namespace Query\Application\Auth\Firm\Program;

use Query\Application\Auth\Firm\ParticipantRepository as InterfaceForFirm;

interface ParticipantRepository extends InterfaceForFirm
{

    public function containRecordOfActiveParticipantCorrespondWithClient(
            string $firmId, string $programId, string $clientId): bool;

    public function containRecordOfActiveParticipantCorrespondWithUser(string $firmId, string $programId, string $userId): bool;
    
    public function containRecordOfActiveParticipantCorrespondWithTeam(string $teamId, string $programId): bool;
}
