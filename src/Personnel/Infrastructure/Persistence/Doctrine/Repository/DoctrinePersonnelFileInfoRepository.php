<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\PersonnelFileInfoRepository,
    Domain\Model\Firm\Personnel\PersonnelFileInfo,
    Domain\Service\PersonnelFileInfoRepository as InterfaceForDomainService
};
use Resources\ {
    Exception\RegularException,
    Uuid
};
use Shared\Domain\Model\FileInfo;

class DoctrinePersonnelFileInfoRepository extends EntityRepository implements PersonnelFileInfoRepository, InterfaceForDomainService
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

    public function fileInfoOf(
            PersonnelCompositionId $personnelCompositionId, string $personnelFileInfoId): FileInfo
    {
        $parameters = [
            "personnelFileInfoId" => $personnelFileInfoId,
            "personnelId" => $personnelCompositionId->getPersonnelId(),
            "firmId" => $personnelCompositionId->getFirmId(),
        ];
        
        $subQuery = $this->createQueryBuilder('personnelFileInfo');
        $subQuery->select('tFileInfo.id')
                ->leftJoin('personnelFileInfo.fileInfo', 'tFileInfo')
                ->andWhere($subQuery->expr()->eq('personnelFileInfo.removed', 'false'))
                ->andWhere($subQuery->expr()->eq('personnelFileInfo.id', ':personnelFileInfoId'))
                ->leftJoin('personnelFileInfo.personnel', 'personnel')
                ->andWhere($subQuery->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($subQuery->expr()->eq('firm.id', ':clientId'))
                ->setMaxResults(1);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('fileInfo')
                ->from(FileInfo::class, 'fileInfo')
                ->andWhere($qb->expr()->in('fileInfo.id', $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: file info not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
