<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\FirmRepository,
    Domain\Model\Firm
};
use Resources\Exception\RegularException;

class DoctrineFirmRepository extends EntityRepository implements FirmRepository
{
    
    public function ofId(string $firmId): Firm
    {
        $params = [
            "firmId" => $firmId,
        ];
        $qb = $this->createQueryBuilder('firm');
        $qb->select('firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: firm not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
