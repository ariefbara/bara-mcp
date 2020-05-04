<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Personnel\{
    Application\Listener\PersonnelNotificationRepository,
    Domain\Model\Firm\Personnel\PersonnelNotification
};
use Resources\Uuid;

class DoctrinePersonnelNotificationRepository extends EntityRepository implements PersonnelNotificationRepository
{

    public function add(PersonnelNotification $personnelNotification): void
    {
        $em = $this->getEntityManager();
        $em->persist($personnelNotification);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
