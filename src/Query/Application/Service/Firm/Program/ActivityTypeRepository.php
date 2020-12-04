<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\ActivityType;

interface ActivityTypeRepository
{

    public function anActivityTypeInProgram(string $programId, string $activityTypeId): ActivityType;

    public function allActivityTypesInProgram(string $programId, int $page, int $pageSize, ?bool $enabledOnly = true);
}
