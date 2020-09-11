<?php

namespace Query\Application\Service\Firm\Program\Mission;

use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

interface LearningMaterialRepository
{

    public function ofId(string $firmId, string $programId, string $missionId, string $learningMaterialId): LearningMaterial;
    
    public function all(string $firmId, string $programId, string $missionId, int $page, int $pageSize);
}
