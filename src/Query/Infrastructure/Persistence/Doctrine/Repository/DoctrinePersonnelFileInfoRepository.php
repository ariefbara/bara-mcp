<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Personnel\PersonnelFileInfoRepository,
    Domain\Model\Firm\Personnel\PersonnelFileInfo
};
use Resources\Exception\RegularException;

class DoctrinePersonnelFileInfoRepository extends EntityRepository implements PersonnelFileInfoRepository
{

    public function ofId(string $firmId, string $personnelId, string $personnelFileInfoId): PersonnelFileInfo
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "personnelFileInfoId" => $personnelFileInfoId,
        ];

        $qb = $this->createQueryBuilder('personnelFileInfo');
        $qb->select('personnelFileInfo')
                ->andWhere($qb->expr()->eq('personnelFileInfo.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnelFileInfo.id', ':personnelFileInfoId'))
                ->leftJoin('personnelFileInfo.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: personnel file info not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
