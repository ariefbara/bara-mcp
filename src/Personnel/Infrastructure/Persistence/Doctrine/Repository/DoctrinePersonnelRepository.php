<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Personnel\ {
    Application\Service\Firm\PersonnelRepository,
    Domain\Model\Firm\Personnel
};
use Resources\Exception\RegularException;

class DoctrinePersonnelRepository extends EntityRepository implements PersonnelRepository
{

    public function ofId(string $firmId, string $personnelId): Personnel
    {
        $parameters = [
            "personnelId" => $personnelId,
            "firmId" => $firmId,
        ];

        $qb = $this->createQueryBuilder('personnel');
        $qb->select('personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: personnel not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
