<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\{
    Application\Service\PersonnelMailRepository,
    Domain\Model\Firm\Personnel\PersonnelMail
};
use Resources\Uuid;

class DoctrinePersonnelMailRepository extends EntityRepository implements PersonnelMailRepository
{

    public function add(PersonnelMail $personnelMail): void
    {
        $em = $this->getEntityManager();
        $em->persist($personnelMail);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
