<?php

namespace ActivityCreator\Domain\service;

use ActivityCreator\Domain\DependencyModel\Firm\Manager;

interface ManagerRepository
{
    public function ofId(string $managerId): Manager;
}
