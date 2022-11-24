<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Participant\Domain\Model\Participant\Task;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\TaskRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineTaskRepository extends DoctrineEntityRepository implements TaskRepository
{

    public function ofId(string $id): Task
    {
        return $this->findOneByIdOrDie($id, 'task');
    }

}
