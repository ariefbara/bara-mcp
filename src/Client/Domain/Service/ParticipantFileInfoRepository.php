<?php

namespace Client\Domain\Service;

use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Shared\Domain\Model\FileInfo;

interface ParticipantFileInfoRepository
{

    public function fileInfoOf(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participationFileInfoId): FileInfo;
}
