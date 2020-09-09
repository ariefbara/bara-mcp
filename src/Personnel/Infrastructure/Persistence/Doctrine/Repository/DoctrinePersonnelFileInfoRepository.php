<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Personnel\ {
    Application\Service\Firm\Personnel\PersonnelFileInfoRepository,
    Domain\Model\Firm\Personnel\PersonnelFileInfo
};
use Resources\Uuid;

class DoctrinePersonnelFileInfoRepository extends EntityRepository implements PersonnelFileInfoRepository
{
    
    public function add(PersonnelFileInfo $personnelFileInfo): void
    {
        $em = $this->getEntityManager();
        $em->persist($personnelFileInfo);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
