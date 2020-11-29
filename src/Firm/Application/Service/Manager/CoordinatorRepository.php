<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\Coordinator;

interface CoordinatorRepository
{
    public function aCoordinatorOfId(string $coordinatorId): Coordinator;
    
    public function update(): void;
}
