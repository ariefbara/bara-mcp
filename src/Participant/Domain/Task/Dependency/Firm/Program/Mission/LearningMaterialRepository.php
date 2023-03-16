<?php

namespace Participant\Domain\Task\Dependency\Firm\Program\Mission;

use Participant\Domain\DependencyModel\Firm\Program\Mission\LearningMaterial;

interface LearningMaterialRepository
{

    public function ofId(string $id): LearningMaterial;
}
