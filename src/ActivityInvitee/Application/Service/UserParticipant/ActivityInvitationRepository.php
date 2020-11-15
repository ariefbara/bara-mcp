<?php

namespace ActivityInvitee\Application\Service\UserParticipant;

use ActivityInvitee\Domain\Model\ParticipantInvitee;

interface ActivityInvitationRepository
{

    public function anInvitationBelongsToUser(string $userId, string $invitationId): ParticipantInvitee;

    public function update(): void;
}
