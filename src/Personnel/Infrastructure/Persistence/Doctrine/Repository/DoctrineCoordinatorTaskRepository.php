<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorTask;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Coordinator\CoordinatorTaskRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineCoordinatorTaskRepository extends DoctrineEntityRepository implements CoordinatorTaskRepository
{
    
    public function add(CoordinatorTask $coordinatorTask): void
    {
        $this->persist($coordinatorTask);
    }

    public function ofId(string $id): CoordinatorTask
    {
        return $this->findOneByIdOrDie($id, 'coordinator task');
    }

}
