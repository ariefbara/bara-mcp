<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

interface MissionSummaryRepository
{
    public function ofParticipantId(string $participantId): ?array;
}
