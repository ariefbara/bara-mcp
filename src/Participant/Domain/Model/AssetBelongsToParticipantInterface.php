<?php

namespace Participant\Domain\Model;

interface AssetBelongsToParticipantInterface
{
    public function belongsTo(Participant $participant): bool;
}
