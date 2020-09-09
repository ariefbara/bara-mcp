<?php

namespace SharedContext\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\Domain\Model\ {
    Firm\Client\ClientFileInfo,
    Firm\Personnel\PersonnelFileInfo,
    User\UserFileInfo
};
use Resources\Exception\RegularException;
use SharedContext\ {
    Application\Service\FileInfoRepository,
    Domain\Model\SharedEntity\FileInfo
};

class DoctrineFileInfoRepository extends EntityRepository implements FileInfoRepository
{
    
    public function fileInfoOfClient(string $firmId, string $clientId, string $fileInfoId): FileInfo
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'fileInfoId' => $fileInfoId,
        ];
        
        $clientFileInfoQb = $this->getEntityManager()->createQueryBuilder();
        $clientFileInfoQb->select('tFileInfo.id')
                ->from(ClientFileInfo::class, 'clientFileInfo')
                ->leftJoin('clientFileInfo.fileInfo', 'tFileInfo')
                ->andWhere($clientFileInfoQb->expr()->eq('tFileInfo.id', ':fileInfoId'))
                ->leftJoin('clientFileInfo.client', 'client')
                ->andWhere($clientFileInfoQb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($clientFileInfoQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $qb  = $this->createQueryBuilder('fileInfo');
        $qb->select('fileInfo')
                ->andWhere($qb->expr()->in('fileInfo.id', $clientFileInfoQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: file info not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function fileInfoOfUser(string $userId, string $fileInfoId): FileInfo
    {
        $params = [
            'userId' => $userId,
            'fileInfoId' => $fileInfoId,
        ];
        
        $userFileInfoQb = $this->getEntityManager()->createQueryBuilder();
        $userFileInfoQb->select('tFileInfo.id')
                ->from(UserFileInfo::class, 'userFileInfo')
                ->leftJoin('userFileInfo.fileInfo', 'tFileInfo')
                ->andWhere($userFileInfoQb->expr()->eq('tFileInfo.id', ':fileInfoId'))
                ->leftJoin('userFileInfo.user', 'user')
                ->andWhere($userFileInfoQb->expr()->eq('user.id', ':userId'))
                ->setMaxResults(1);
        
        $qb  = $this->createQueryBuilder('fileInfo');
        $qb->select('fileInfo')
                ->andWhere($qb->expr()->in('fileInfo.id', $userFileInfoQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: file info not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aFileInfoOfPersonnel(string $firmId, string $personnelId, string $fileInfoId): FileInfo
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'fileInfoId' => $fileInfoId,
        ];
        
        $clientFileInfoQb = $this->getEntityManager()->createQueryBuilder();
        $clientFileInfoQb->select('tFileInfo.id')
                ->from(PersonnelFileInfo::class, 'personnelFileInfo')
                ->leftJoin('personnelFileInfo.fileInfo', 'tFileInfo')
                ->andWhere($clientFileInfoQb->expr()->eq('tFileInfo.id', ':fileInfoId'))
                ->leftJoin('personnelFileInfo.personnel', 'personnel')
                ->andWhere($clientFileInfoQb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($clientFileInfoQb->expr()->eq('firm.id', ':firmId'))
                ->setMaxResults(1);
        
        $qb  = $this->createQueryBuilder('fileInfo');
        $qb->select('fileInfo')
                ->andWhere($qb->expr()->in('fileInfo.id', $clientFileInfoQb->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: file info not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
