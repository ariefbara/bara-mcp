<?php

namespace User\Application\Service\Personnel\Coordinator;

use User\Domain\Model\Personnel\Coordinator;

interface CoordinatorRepository
{

    public function aCoordinatorBelongsToPersonnel(string $firmId, string $personnelId, string $coordinatorId): Coordinator;
    
    public function update(): void;
}
