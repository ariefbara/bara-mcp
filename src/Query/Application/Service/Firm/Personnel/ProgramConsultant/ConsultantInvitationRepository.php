<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;

interface ConsultantInvitationRepository
{

    public function anInvitationForConsultant(string $firmId, string $personnelId, string $invitationId): ConsultantInvitee;

    public function allInvitationsForConsultant(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize,
            ?InviteeFilter $inviteeFilter);
}
