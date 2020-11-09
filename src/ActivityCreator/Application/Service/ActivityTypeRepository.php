<?php

namespace ActivityCreator\Application\Service;

use ActivityCreator\Domain\DependencyModel\Firm\Program\ActivityType;

interface ActivityTypeRepository
{

    public function ofId(string $activityTypeId): ActivityType;
}
