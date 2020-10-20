<?php

namespace Team\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Resources\Uuid;
use Team\ {
    Application\Service\Team\TeamFileInfoRepository,
    Domain\Model\Team\TeamFileInfo
};

class DoctrineTeamFileInfoRepository extends EntityRepository implements TeamFileInfoRepository
{
    
    public function add(TeamFileInfo $teamFileInfo): void
    {
        $em = $this->getEntityManager();
        $em->persist($teamFileInfo);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
