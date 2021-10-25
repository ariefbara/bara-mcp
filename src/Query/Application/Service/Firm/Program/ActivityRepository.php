<?php

namespace Query\Application\Service\Firm\Program;

use Query\Application\Service\Firm\ActivityRepository as InterfaceForFirm;
use Query\Domain\Model\Firm\Program\Activity;
use Query\Infrastructure\QueryFilter\ActivityFilter;

interface ActivityRepository extends InterfaceForFirm
{

    public function anActivityInProgram(string $firmId, string $programId, string $activityId): Activity;

    public function allActivitiesInProgram(string $firmId, string $programId, int $page, int $pageSize, ActivityFilter $activityFilter);
}
