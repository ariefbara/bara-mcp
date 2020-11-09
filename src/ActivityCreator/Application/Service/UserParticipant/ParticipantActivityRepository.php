<?php

namespace ActivityCreator\Application\Service\UserParticipant;

use ActivityCreator\Domain\Model\ParticipantActivity;

interface ParticipantActivityRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantActivity $participantActivity): void;

    public function aParticipantActivityBelongsToUser(string $userId, string $participantActivityId): ParticipantActivity;
    
    public function update(): void;
}
