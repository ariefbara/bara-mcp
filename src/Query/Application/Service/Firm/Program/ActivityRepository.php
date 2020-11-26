<?php

namespace Query\Application\Service\Firm\Program;

use Query\ {
    Application\Service\Firm\ActivityRepository as InterfaceForFirm,
    Domain\Model\Firm\Program\Activity
};

interface ActivityRepository extends InterfaceForFirm
{
    public function anActivityInProgram(string $firmId, string $programId, string $activityId): Activity;
}
