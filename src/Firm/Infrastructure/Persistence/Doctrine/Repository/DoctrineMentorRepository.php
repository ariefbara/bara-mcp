<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\MentorRepository,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Program\Mentor
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineMentorRepository extends EntityRepository implements MentorRepository
{
    public function ofId(ProgramCompositionId $programCompositionId, string $mentorId): Mentor
    {
        $qb = $this->createQueryBuilder('mentor');
        $qb->select('mentor')
            ->andWhere($qb->expr()->eq('mentor.removed', 'false'))
            ->andWhere($qb->expr()->eq('mentor.id', ':mentorId'))
            ->setParameter('mentorId', $mentorId)
            ->leftJoin('mentor.program', 'program')
            ->andWhere($qb->expr()->eq('program.removed', 'false'))
            ->andWhere($qb->expr()->eq('program.id', ':programId'))
            ->setParameter('programId', $programCompositionId->getProgramId())
            ->leftJoin('program.firm', 'firm')
            ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
            ->setParameter('firmId', $programCompositionId->getFirmId())
            ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: mentor not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('mentor');
        $qb->select('mentor')
            ->andWhere($qb->expr()->eq('mentor.removed', 'false'))
            ->leftJoin('mentor.program', 'program')
            ->andWhere($qb->expr()->eq('program.removed', 'false'))
            ->andWhere($qb->expr()->eq('program.id', ':programId'))
            ->setParameter('programId', $programCompositionId->getProgramId())
            ->leftJoin('program.firm', 'firm')
            ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
            ->setParameter('firmId', $programCompositionId->getFirmId());
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
