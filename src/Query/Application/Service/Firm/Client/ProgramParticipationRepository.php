<?php

namespace Query\Application\Service\Firm\Client;

use Query\ {
    Application\Service\Firm\Client\AsProgramParticipant\ClientProgramParticipationRepository,
    Domain\Model\Firm\Client\ClientParticipant
};

interface ProgramParticipationRepository extends ClientProgramParticipationRepository
{

    public function ofId(string $firmId, string $clientId, string $programParticipationId): ClientParticipant;

    public function all(string $firmId, string $clientId, int $page, int $pageSize, ?bool $activeStatus);
}
