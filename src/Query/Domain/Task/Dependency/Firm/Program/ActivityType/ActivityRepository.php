<?php

namespace Query\Domain\Task\Dependency\Firm\Program\ActivityType;

use Query\Domain\Model\Firm\Program\Activity;

interface ActivityRepository
{

    public function activityDetailInProgram(string $programId, string $id): Activity;
}
