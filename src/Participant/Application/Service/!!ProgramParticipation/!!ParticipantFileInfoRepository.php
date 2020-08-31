<?php

namespace User\Application\Service\User\ProgramParticipation;

use User\Domain\Model\User\ProgramParticipation\ParticipantFileInfo;

interface ParticipantFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantFileInfo $programParticipationFileInfo): void;
}
