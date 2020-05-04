<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\{
    Application\Service\Firm\Program\RegistrationPhaseRepository,
    Domain\Model\Firm\Program\RegistrationPhase
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineRegistrationPhaseRepository extends EntityRepository implements RegistrationPhaseRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $registrationPhaseId): RegistrationPhase
    {
        $qb = $this->createQueryBuilder('registrationPhase');
        $qb->select('registrationPhase')
                ->andWhere($qb->expr()->eq('registrationPhase.removed', 'false'))
                ->andWhere($qb->expr()->eq('registrationPhase.id', ':registrationPhaseId'))
                ->setParameter('registrationPhaseId', $registrationPhaseId)
                ->leftJoin('registrationPhase.program', 'program')
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
            $errorDetail = "not found: registration phase not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('registrationPhase');
        $qb->select('registrationPhase')
                ->andWhere($qb->expr()->eq('registrationPhase.removed', 'false'))
                ->leftJoin('registrationPhase.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $programCompositionId->getFirmId());

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
