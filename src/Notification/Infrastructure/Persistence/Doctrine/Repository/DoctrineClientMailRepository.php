<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\Client\ClientMailRepository,
    Domain\Model\Firm\Client\ClientMail
};
use Resources\Uuid;

class DoctrineClientMailRepository extends EntityRepository implements ClientMailRepository
{
    
    public function add(ClientMail $clientMail): void
    {
        $em = $this->getEntityManager();
        $em->persist($clientMail);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
