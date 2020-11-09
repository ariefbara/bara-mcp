<?php

namespace ActivityCreator\Application\Service\ClientParticipant;

use ActivityCreator\Domain\Model\ParticipantActivity;

interface ParticipantActivityRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantActivity $participantActivity): void;

    public function aParticipantActivityBelongsToClient(string $firmId, string $clientId, string $participantActivityId): ParticipantActivity;
    
    public function update(): void;
}
