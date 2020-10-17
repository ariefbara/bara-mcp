<?php

namespace Personnel\Domain\Model\Firm\Personnel;

interface AssetBelongsToParticipantInProgram
{
    public function belongsToParticipantInProgram(string $programId): bool;
}
