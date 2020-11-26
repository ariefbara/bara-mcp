<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\{
    Application\Service\PersonnelRepository,
    Domain\Model\Firm\Personnel
};
use Resources\Exception\RegularException;

class DoctrinePersonnelRepository extends EntityRepository implements PersonnelRepository
{

    public function ofId(string $personnelId): Personnel
    {
        $personnel = $this->findOneBy(["id" => $personnelId]);
        if (empty($personnel)) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
        return $personnel;
    }

}
