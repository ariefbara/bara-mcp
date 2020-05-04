<?php

namespace Firm\Application\Service\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;

interface LearningMaterialRepository
{

    public function nextIdentity(): string;

    public function add(LearningMaterial $learningMaterial): void;

    public function update(): void;

    public function ofId(MissionCompositionId $missionCompositionId, string $learningMaterialId): LearningMaterial;
}
