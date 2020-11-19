<?php

namespace Firm\Application\Service\Personnel;

use Firm\Domain\Model\Firm\Program\ActivityType;

interface ActivityTypeRepository
{

    public function ofId(string $activityTypeId): ActivityType;
}
