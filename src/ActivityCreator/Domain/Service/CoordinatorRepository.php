<?php

namespace ActivityCreator\Domain\service;

use ActivityCreator\Domain\DependencyModel\Firm\Personnel\Coordinator;

interface CoordinatorRepository
{
    public function ofId(string $coordinatorId): Coordinator;
}
