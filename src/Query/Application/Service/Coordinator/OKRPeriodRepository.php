<?php

namespace Query\Application\Service\Coordinator;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;

interface OKRPeriodRepository
{

    public function anOKRPeriodInProgram(string $programId, string $okrPeriodId): OKRPeriod;

    public function allOKRPeriodsBelongsToParticipantInProgram(
            string $programId, string $participantId, int $page, int $pageSize);
}
