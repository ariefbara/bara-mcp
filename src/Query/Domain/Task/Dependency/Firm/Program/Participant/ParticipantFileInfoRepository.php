<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\ParticipantFileInfo;

interface ParticipantFileInfoRepository
{

    public function aParticipantFileInfoBelongsToParticipant(string $participantId, string $id): ParticipantFileInfo;
}
