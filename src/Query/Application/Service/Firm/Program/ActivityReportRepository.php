<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;

interface ActivityReportRepository
{

    public function anActivityReportInProgram(string $firmId, string $programId, string $activityReportId): InviteeReport;

    public function allActivityReportInActivity(
            string $firmId, string $programId, string $activityId, int $page, int $pageSize);
}
