<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineProgramParticipationRepository extends EntityRepository implements ProgramParticipationRepository
{
    public function ofId(string $clientId, string $programParticipationId): ProgramParticipation
    {
        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
            ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
            ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
            ->setParameter('programParticipationId', $programParticipationId)
            ->leftJoin('programParticipation.client', 'client')
            ->andWhere($qb->expr()->eq('client.id', ':clientId'))
            ->setParameter('clientId', $clientId)
            ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program participation not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function all(string $clientId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('programParticipation');
        $qb->select('programParticipation')
            ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
            ->leftJoin('programParticipation.client', 'client')
            ->andWhere($qb->expr()->eq('client.id', ':clientId'))
            ->setParameter('clientId', $clientId);
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
            
    }

}
