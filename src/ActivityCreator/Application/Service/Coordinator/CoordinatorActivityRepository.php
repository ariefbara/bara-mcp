<?php

namespace ActivityCreator\Application\Service\Coordinator;

use ActivityCreator\Domain\Model\CoordinatorActivity;

interface CoordinatorActivityRepository
{

    public function nextIdentity(): string;

    public function add(CoordinatorActivity $coordinatorActivity): void;

    public function aCoordinatorActivityBelongsToPersonnel(string $firmId, string $personnelId,
            string $coordinatorActivityId): CoordinatorActivity;

    public function update(): void;
}
