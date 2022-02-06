<?php

namespace Firm\Domain\Task\Dependency\Firm\Program\Mission;

use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;

interface LearningMaterialRepository
{

    public function nextIdentity(): string;

    public function add(LearningMaterial $learningMaterial): void;

    public function aLearningMaterialOfId(string $id): LearningMaterial;
}
