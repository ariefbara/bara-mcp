<?php

namespace ActivityCreator\Application\Service\Coordinator;

use ActivityCreator\Domain\DependencyModel\Firm\Personnel\Coordinator;

interface CoordinatorRepository
{

    public function aCoordinatorBelongsToPersonnel(string $firmId, string $personnelId, string $coordinatorId): Coordinator;
}
