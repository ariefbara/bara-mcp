<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\{
    Application\Service\Personnel\ActivityTypeRepository as InterfaceForPersonnel,
    Domain\Model\Firm\Program\ActivityType
};

interface ActivityTypeRepository extends InterfaceForPersonnel
{

    public function nextIdentity(): string;

    public function add(ActivityType $activityType): void;

    public function ofId(string $activityTypeId): ActivityType;
}
