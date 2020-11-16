<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Resources\Uuid;
use User\{
    Application\Service\Manager\ManagerFileInfoRepository,
    Domain\Model\Manager\ManagerFileInfo
};

class DoctrineManagerFileInfoRepository extends EntityRepository implements ManagerFileInfoRepository
{

    public function add(ManagerFileInfo $managerFileInfo): void
    {
        $em = $this->getEntityManager();
        $em->persist($managerFileInfo);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
