<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\Model\Participant\LearningProgress;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\LearningProgressRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineLearningProgressRepository extends DoctrineEntityRepository implements LearningProgressRepository
{

    public function ofId(string $id): LearningProgress
    {
        return $this->findOneByIdOrDie($id, 'learning progress');
    }

}
