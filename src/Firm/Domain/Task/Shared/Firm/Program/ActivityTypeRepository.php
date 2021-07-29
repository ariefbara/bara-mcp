<?php

namespace Firm\Domain\Task\Shared\Firm\Program;

use Firm\Domain\Model\Firm\Program\ActivityType;

interface ActivityTypeRepository
{
    public function ofId(string $id): ActivityType;
}
