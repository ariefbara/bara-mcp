<?php

namespace Bara\Infrastructure\Persistence\Doctrine\Repository;

use Bara\{
    Application\Service\FirmRepository,
    Domain\Model\Firm
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder,
    Uuid
};

class DoctrineFirmRepository extends EntityRepository implements FirmRepository
{

    public function add(Firm $firm): void
    {
        $em = $this->getEntityManager();
        $em->persist($firm);
        $em->flush();
    }

    public function all(int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('firm');
        $qb->select('firm');
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
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

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function containRecordOfIdentifier(string $identifier): bool
    {
        $qb = $this->createQueryBuilder('firm');
        $qb->select('1')
            ->andWhere($qb->expr()->eq('firm.identifier', ':identifier'))
            ->setParameter('identifier', $identifier)
            ->setMaxResults(1);
        return !empty($qb->getQuery()->getResult());
    }

}
