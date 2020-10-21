<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\Client\ClientRepository,
    Domain\Model\Firm\Client
};

class DoctrineClientRepository extends EntityRepository implements ClientRepository
{

    public function ofId(string $clientId): Client
    {
        return $this->findOneBy(["id" => $clientId]);
    }

}
