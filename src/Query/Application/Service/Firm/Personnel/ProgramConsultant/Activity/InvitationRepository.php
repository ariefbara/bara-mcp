<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant\Activity;

use Query\Domain\Model\Firm\Program\Activity\Invitee;

interface InvitationRepository
{

    public function anInvitationFromConsultant(string $firmId, string $personnelId, string $invitationId): Invitee;

    public function allInvitationsInConsultantActivity(
            string $firmId, string $personnelId, string $activityId, int $page, int $pageSize);
}
