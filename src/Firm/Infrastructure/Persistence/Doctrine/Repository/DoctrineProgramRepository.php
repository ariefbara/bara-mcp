<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineProgramRepository extends EntityRepository implements ProgramRepository
{

    public function add(Program $program): void
    {
        $em = $this->getEntityManager();
        $em->persist($program);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $firmId, string $programId): Program
    {
        $qb = $this->createQueryBuilder('program');
        $qb->select('program')
            ->andWhere($qb->expr()->eq('program.removed', 'false'))
            ->andWhere($qb->expr()->eq('program.id', ':programId'))
            ->setParameter('programId', $programId)
            ->leftJoin('program.firm', 'firm')
            ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
            ->setParameter('firmId', $firmId)
            ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
