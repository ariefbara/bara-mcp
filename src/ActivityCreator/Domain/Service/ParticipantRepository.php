<?php

namespace ActivityCreator\Domain\service;

use ActivityCreator\Domain\DependencyModel\Firm\Program\Participant;

interface ParticipantRepository
{
    public function ofId(string $participantId): Participant;
}
