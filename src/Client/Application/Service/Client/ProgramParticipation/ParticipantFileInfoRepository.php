<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ParticipantFileInfo;

interface ParticipantFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantFileInfo $programParticipationFileInfo): void;
}
