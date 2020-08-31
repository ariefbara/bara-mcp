<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Firm\Client\ClientFileInfoRepository,
    Domain\Model\Firm\Client\ClientFileInfo
};
use Resources\Exception\RegularException;

class DoctrineClientFileInfoRepository extends EntityRepository implements ClientFileInfoRepository
{

    public function ofId(string $firmId, string $clientId, string $clientFileInfoId): ClientFileInfo
    {
        $params = [
            'firmId' => $firmId,
            'clientId' => $clientId,
            'clientFileInfoId' => $clientFileInfoId,
        ];

        $qb = $this->createQueryBuilder('clientFileInfo');
        $qb->select('clientFileInfo')
                ->andWhere($qb->expr()->eq('clientFileInfo.id', ':clientFileInfoId'))
                ->leftJoin('clientFileInfo.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->leftJoin('client.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: client file info not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
