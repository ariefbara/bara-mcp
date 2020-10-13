<?php

namespace Participant\Domain\Model;

interface AssetBelongsToParticipantInterface
{
    public function belongsToParticipant(Participant $participant): bool;
}
