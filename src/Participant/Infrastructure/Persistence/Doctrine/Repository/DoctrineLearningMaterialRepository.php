<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\DependencyModel\Firm\Program\Mission\LearningMaterial;
use Participant\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineLearningMaterialRepository extends DoctrineEntityRepository implements LearningMaterialRepository
{

    public function ofId(string $id): LearningMaterial
    {
        return $this->findOneByIdOrDie($id, 'learning material');
    }

}
