<?php

namespace ActivityInvitee\Application\Service\Consultant;

use ActivityInvitee\Domain\Model\ConsultantInvitee;

interface ActivityInvitationRepository
{
    public function anInvitationBelongsToPersonnel(string $firmId, string $personnelId, string $invitationId): ConsultantInvitee;
    
    public function update(): void;
}
