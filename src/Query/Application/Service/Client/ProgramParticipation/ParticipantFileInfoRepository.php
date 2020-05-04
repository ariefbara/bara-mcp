<?php

namespace Query\Application\Service\Client\ProgramParticipation;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\Domain\Model\Firm\Program\Participant\ParticipantFileInfo;

interface ParticipantFileInfoRepository
{

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participantFileInfoId): ParticipantFileInfo;
}
