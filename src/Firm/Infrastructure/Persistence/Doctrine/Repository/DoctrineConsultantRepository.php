<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\ConsultantRepository,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Program\Consultant
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultantRepository extends EntityRepository implements ConsultantRepository
{
    public function ofId(ProgramCompositionId $programCompositionId, string $consultantId): Consultant
    {
        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
            ->andWhere($qb->expr()->eq('consultant.removed', 'false'))
            ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
            ->setParameter('consultantId', $consultantId)
            ->leftJoin('consultant.program', 'program')
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
            $errorDetail = "not found: consultant not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
            ->andWhere($qb->expr()->eq('consultant.removed', 'false'))
            ->leftJoin('consultant.program', 'program')
            ->andWhere($qb->expr()->eq('program.removed', 'false'))
            ->andWhere($qb->expr()->eq('program.id', ':programId'))
            ->setParameter('programId', $programCompositionId->getProgramId())
            ->leftJoin('program.firm', 'firm')
            ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
            ->setParameter('firmId', $programCompositionId->getFirmId());
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
