<?php

namespace ActivityCreator\Infrastructure\Persistence\Doctrine\Repository;

use ActivityCreator\{
    Application\Service\ActivityTypeRepository,
    Domain\DependencyModel\Firm\Program\ActivityType
};
use Doctrine\ORM\EntityRepository;
use Resources\Exception\RegularException;

class DoctrineActivityTypeRepository extends EntityRepository implements ActivityTypeRepository
{

    public function ofId(string $activityTypeId): ActivityType
    {
        $activityType = $this->findOneBy(["id" => $activityTypeId]);
        if (empty($activityType)) {
            $errorDetail = "not found: activity type not found";
            throw RegularException::notFound($errorDetail);
        }
        return $activityType;
    }

}
