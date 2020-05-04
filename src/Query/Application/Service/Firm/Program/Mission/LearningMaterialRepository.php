<?php

namespace Query\Application\Service\Firm\Program\Mission;

use Firm\Application\Service\Firm\Program\Mission\MissionCompositionId;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

interface LearningMaterialRepository
{

    public function ofId(MissionCompositionId $missionCompositionId, string $learningMaterialId): LearningMaterial;

    public function all(MissionCompositionId $missionCompositionId, int $page, int $pageSize);
}
