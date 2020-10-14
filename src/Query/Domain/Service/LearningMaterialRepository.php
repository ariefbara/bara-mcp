<?php

namespace Query\Domain\Service;

use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

interface LearningMaterialRepository
{
    public function aLearningMaterialBelongsToProgram(string $programId, string $learningMaterialId): LearningMaterial;
}
