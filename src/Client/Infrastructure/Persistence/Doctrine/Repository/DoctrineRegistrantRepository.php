<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\Domain\DependencyModel\Firm\Program\Registrant;
use Client\Domain\Task\Repository\Firm\Program\RegistrantRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineRegistrantRepository extends DoctrineEntityRepository implements RegistrantRepository
{

    public function ofId(string $id): Registrant
    {
        return $this->findOneByIdOrDie($id, 'registrant');
    }

}
