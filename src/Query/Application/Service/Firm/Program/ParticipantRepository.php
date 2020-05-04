<?php

namespace Query\Application\Service\Firm\Program;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Domain\Model\Firm\Program\Participant;

interface ParticipantRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $participantId): Participant;

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize);
}
