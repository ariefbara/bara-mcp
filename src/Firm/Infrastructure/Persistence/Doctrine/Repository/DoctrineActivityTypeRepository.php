<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\{
    Application\Service\Manager\ActivityTypeRepository,
    Domain\Model\Firm\Program\ActivityType
};
use Resources\Uuid;

class DoctrineActivityTypeRepository extends EntityRepository implements ActivityTypeRepository
{

    public function add(ActivityType $activityType): void
    {
        $em = $this->getEntityManager();
        $em->persist($activityType);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
