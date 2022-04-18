<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Domain\DependencyModel\Firm\Program;
use Client\Domain\Task\Repository\Firm\ProgramRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineProgramRepository extends DoctrineEntityRepository implements ProgramRepository
{

    public function ofId(string $id): Program
    {
        return $this->findOneByIdOrDie($id, 'program');
    }

}
