<?php

namespace Query\Application\Service\Firm\Personnel\ProgramConsultant;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantActivity;

interface ConsultantActivityRepository
{

    public function anActivityBelongsToConsultant(string $firmId, string $personnelId, string $activityId): ConsultantActivity;

    public function allActivitiesBelongsToConsultant(
            string $firmId, string $personnelId, string $consultantId, int $page, int $pageSize);
}
