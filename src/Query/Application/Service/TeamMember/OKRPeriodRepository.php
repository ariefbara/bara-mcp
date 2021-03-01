<?php

namespace Query\Application\Service\TeamMember;

use Query\Domain\Model\Firm\Program\Participant\OKRPeriod;

interface OKRPeriodRepository
{

    public function anOKRPeriodBelongsToParticipant(string $participantId, string $okrPeriodId): OKRPeriod;

    public function allOKRPeriodsBelongsToParticipant(string $participantId, int $page, int $pageSize);
}
