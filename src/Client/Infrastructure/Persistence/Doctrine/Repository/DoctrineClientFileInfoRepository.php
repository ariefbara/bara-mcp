<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ClientFileInfoRepository,
    Domain\Model\Client\ClientFileInfo
};
use Doctrine\ORM\EntityRepository;
use Resources\Uuid;

class DoctrineClientFileInfoRepository extends EntityRepository implements ClientFileInfoRepository
{

    public function add(ClientFileInfo $clientFileInfo): void
    {
        $em = $this->getEntityManager();
        $em->persist($clientFileInfo);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
