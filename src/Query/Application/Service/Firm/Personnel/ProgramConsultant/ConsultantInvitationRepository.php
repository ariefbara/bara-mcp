<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitation;

interface ConsultantInvitationRepository
{

    public function anInvitationForConsultant(string $firmId, string $personnelId, string $invitationId): ConsultantInvitation;

    public function allInvitationsForConsultant(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize);
}
