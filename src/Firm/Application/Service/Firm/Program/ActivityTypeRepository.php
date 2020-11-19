<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\{
    Application\Service\Manager\ActivityTypeRepository as InterfaceForManager,
    Application\Service\Personnel\ActivityTypeRepository as InterfaceForPersonnel,
    Domain\Model\Firm\Program\ActivityType
};

interface ActivityTypeRepository extends InterfaceForPersonnel, InterfaceForManager
{

    public function ofId(string $activityTypeId): ActivityType;
}
