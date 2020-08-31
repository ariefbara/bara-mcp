<?php

namespace User\Domain\Service;

use User\Application\Service\User\ProgramParticipation\ProgramParticipationCompositionId;
use Shared\Domain\Model\FileInfo;

interface ParticipantFileInfoRepository
{

    public function fileInfoOf(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $participationFileInfoId): FileInfo;
}
