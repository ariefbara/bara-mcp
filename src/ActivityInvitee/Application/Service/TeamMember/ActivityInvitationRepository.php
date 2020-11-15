<?php

namespace ActivityInvitee\Application\Service\TeamMember;

use ActivityInvitee\Domain\Model\ParticipantInvitee;

interface ActivityInvitationRepository
{

    public function ofId(string $invitationId): ParticipantInvitee;

    public function update(): void;
}
