<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ProgramParticipationFileInfo;

interface ProgramParticipationFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(ProgramParticipationFileInfo $programParticipationFileInfo): void;
}
