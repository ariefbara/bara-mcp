<?php

namespace Query\Application\Service\Client;

use Query\Domain\Model\Firm\Program\Participant;

interface ProgramParticipationRepository
{

    public function aProgramParticipationOfClient(string $clientId, string $programParticipationId): Participant;

    public function allProgramParticipationsOfClient(string $clientId, int $page, int $pageSize);
}
