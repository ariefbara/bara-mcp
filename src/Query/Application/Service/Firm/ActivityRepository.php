<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\Program\Activity;

interface ActivityRepository
{

    public function anActivityInFirm(string $firmId, string $activityId): Activity;
}
