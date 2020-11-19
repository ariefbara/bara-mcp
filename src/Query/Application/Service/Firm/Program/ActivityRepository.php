<?php

namespace Query\Application\Service\Firm\Program;

use Query\Domain\Model\Firm\Program\Activity;

interface ActivityRepository
{
    public function anActivityInProgram(string $firmId, string $programId, string $activityId): Activity;
}
