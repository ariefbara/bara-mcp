<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Firm\{
    Application\Service\Firm\Program\ActivityTypeRepository,
    Domain\Model\Firm\Program\ActivityType
};
use Resources\{
    Exception\RegularException,
    Uuid
};

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

    public function ofId(string $activityTypeId): ActivityType
    {
        $activityType = $this->findOneBy(["id" => $activityTypeId]);
        if (empty($activityType)) {
            $errorDetail = "not found: activity type not found";
            throw RegularException::notFound($errorDetail);
        }
        return $activityType;
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
