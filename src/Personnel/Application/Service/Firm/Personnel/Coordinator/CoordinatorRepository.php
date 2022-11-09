<?php

namespace Personnel\Application\Service\Firm\Personnel\Coordinator;

use Personnel\Domain\Model\Firm\Personnel\Coordinator;

interface CoordinatorRepository
{

    public function aCoordinatorBelongsToPersonnel(string $personnelId, string $coordinatorId): Coordinator;

    public function update(): void;
}
