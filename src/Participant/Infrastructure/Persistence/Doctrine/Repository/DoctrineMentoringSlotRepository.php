<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\DependencyModel\Firm\Program\Consultant\MentoringSlot;
use Participant\Domain\Task\Dependency\Firm\Program\Consultant\MentoringSlotRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineMentoringSlotRepository extends DoctrineEntityRepository implements MentoringSlotRepository
{

    public function ofId(string $id): MentoringSlot
    {
        return $this->findOneByIdOrDie($id, 'mentoring slot');
    }

}
