<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\ActivityType;

interface ActivityTypeRepository
{

    public function nextIdentity(): string;

    public function add(ActivityType $activityType): void;
}
