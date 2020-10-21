<?php

namespace Query\Application\Service\Firm\Program;

use Query\ {
    Application\Auth\Firm\Program\ParticipantRepository as InterfaceForAuth,
    Domain\Model\Firm\Program\Participant
};

interface ParticipantRepository extends InterfaceForAuth
{

    public function ofId(string $firmId, string $programId, string $participantId): Participant;

    public function all(string $firmId, string $programId, int $page, int $pageSize, ?bool $activeStatus);
}
