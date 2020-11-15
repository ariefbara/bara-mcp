<?php

namespace ActivityInvitee\Application\Service\ClientParticipant;

use ActivityInvitee\Domain\Model\ParticipantInvitee;

interface ActivityInvitationRepository
{
    public function anInvitationBelongsToClient(string $firmId, string $clientId, string $invitationId): ParticipantInvitee;
    
    public function update(): void;
}
