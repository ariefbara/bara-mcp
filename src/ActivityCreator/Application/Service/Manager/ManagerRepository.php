<?php

namespace ActivityCreator\Application\Service\Manager;

use ActivityCreator\Domain\DependencyModel\Firm\Manager;

interface ManagerRepository
{
    public function aManagerInFirm(string $firmId, string $managerId): Manager;
}
