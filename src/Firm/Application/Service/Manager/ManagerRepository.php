<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Manager;

interface ManagerRepository
{
    public function ofId(string $firmId, string $managerId): Manager;
}
