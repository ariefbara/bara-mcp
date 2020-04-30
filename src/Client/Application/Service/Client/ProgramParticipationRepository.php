<?php

namespace Client\Application\Service\Client;

use Client\Domain\Model\Client\ProgramParticipation;

interface ProgramParticipationRepository
{

    public function update(): void;

    public function ofId(string $clientId, string $programParticipationId): ProgramParticipation;

    public function all(string $clientId, int $page, int $pageSize);

    public function aProgramParticipationOfProgram(string $firmId, string $programId, string $participantId): ProgramParticipation;
}
