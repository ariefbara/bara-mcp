<?php

namespace Query\Application\Service\Firm\Client;

use Query\Domain\Model\Firm\Client\ClientParticipant;

interface ProgramParticipationRepository
{

    public function ofId(string $firmId, string $clientId, string $programParticipationId): ClientParticipant;

    public function all(string $firmId, string $clientId, int $page, int $pageSize);
}
