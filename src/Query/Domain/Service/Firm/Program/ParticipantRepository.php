<?php

namespace Query\Domain\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Participant;

interface ParticipantRepository
{

    public function aParticipantOfProgram(string $programId, string $participantId): Participant;

    public function allParticipantsOfProgram(string $programId, int $page, int $pageSize, ?bool $activeStatus);
}
