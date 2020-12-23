<?php

namespace Participant\Domain\Model;

interface AssetBelongsToParticipant
{

    public function belongsToParticipant(Participant $participant): bool;
}
