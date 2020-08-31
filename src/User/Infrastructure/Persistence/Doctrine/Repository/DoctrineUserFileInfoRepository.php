<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Resources\Uuid;
use User\{
    Application\Service\User\UserFileInfoRepository,
    Domain\Model\User\UserFileInfo
};

class DoctrineUserFileInfoRepository extends EntityRepository implements UserFileInfoRepository
{

    public function add(UserFileInfo $userFileInfo): void
    {
        $em = $this->getEntityManager();
        $em->persist($userFileInfo);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
