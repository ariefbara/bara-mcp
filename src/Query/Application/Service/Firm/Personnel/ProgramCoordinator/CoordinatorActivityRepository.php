<?php

namespace Query\Application\Service\Firm\Personnel\ProgramCoordinator;

use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorActivity;

interface CoordinatorActivityRepository
{

    public function anActivityBelongsToCoordinator(string $firmId, string $personnelId, string $activityId): CoordinatorActivity;

    public function allActivitiesBelongsToCoordinator(
            string $firmId, string $personnelId, string $coordinatorId, int $page, int $pageSize);
}
