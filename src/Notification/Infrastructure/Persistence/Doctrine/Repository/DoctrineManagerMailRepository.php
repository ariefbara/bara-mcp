<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\ManagerMailRepository,
    Domain\Model\Firm\Manager\ManagerMail
};
use Resources\Uuid;

class DoctrineManagerMailRepository extends EntityRepository implements ManagerMailRepository
{
    
    public function add(ManagerMail $managerMail): void
    {
        $em = $this->getEntityManager();
        $em->persist($managerMail);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
