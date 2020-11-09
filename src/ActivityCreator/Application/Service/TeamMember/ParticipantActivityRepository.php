<?php

namespace ActivityCreator\Application\Service\TeamMember;

use ActivityCreator\Domain\Model\ParticipantActivity;

interface ParticipantActivityRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantActivity $participantActivity): void;

    public function update(): void;

    public function ofId(string $participantActivityId): ParticipantActivity;
}
