<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Firm\Application\Listener\ClientRepository as ClientRepository2;
use Firm\Domain\Model\Firm\Client;
use Firm\Domain\Task\Dependency\Firm\ClientRepository;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineClientRepository extends DoctrineEntityRepository implements ClientRepository, ClientRepository2
{

    public function add(Client $client): void
    {
        $this->getEntityManager()->persist($client);
    }

    public function ofId(string $id): Client
    {
        return $this->findOneByIdOrDie($id, 'client');
    }

}
