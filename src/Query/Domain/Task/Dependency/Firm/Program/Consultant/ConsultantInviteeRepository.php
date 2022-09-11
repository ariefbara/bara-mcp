<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant;

interface ConsultantInviteeRepository
{

    public function allInvitationWithPendingReportForPersonnel(
            string $personnelId, int $page, int $pageSize, ConsultantInviteeFilter $consultantInviteeFilter);
}
