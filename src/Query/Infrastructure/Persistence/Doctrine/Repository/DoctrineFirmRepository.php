<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\FirmRepository,
    Domain\Model\Firm
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineFirmRepository extends EntityRepository implements FirmRepository
{

    public function all(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('firm');
        $qb->select('firm');
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $firmId): Firm
    {
        $qb = $this->createQueryBuilder('firm');
        $qb->select('firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $firmId)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: firm not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
