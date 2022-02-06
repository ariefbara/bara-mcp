<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Mission;

use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

interface LearningMaterialRepository
{

    public function aLearningMaterialInFirm(string $firmId, string $id): LearningMaterial;

    /**
     * 
     * @param string $firmId
     * @param LearningMaterialFilter $filter
     * @return LearningMaterial[]
     */
    public function allLearningMaterialInFirm(string $firmId, LearningMaterialFilter $filter);
}
