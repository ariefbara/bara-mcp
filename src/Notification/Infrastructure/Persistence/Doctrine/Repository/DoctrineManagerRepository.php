<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\ManagerRepository,
    Domain\Model\Firm\Manager
};
use Resources\Exception\RegularException;

class DoctrineManagerRepository extends EntityRepository implements ManagerRepository
{
    
    public function ofId(string $managerId): Manager
    {
        $manager = $this->findOneBy(["id" => $managerId]);
        if (empty($manager)) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
        return $manager;
    }

}
