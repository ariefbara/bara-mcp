<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ParticipantNotification;

interface ParticipantNotificationRepository
{

    public function nextIdentity(): string;

    public function add(ParticipantNotification $participantNotification): void;
}
