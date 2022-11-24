<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Participant;

use Participant\Domain\Model\Participant\ParticipantFileInfo;

interface ParticipantFileInfoRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantFileInfo $participantFileInfo): void;

    public function ofId(string $id): ParticipantFileInfo;
}
