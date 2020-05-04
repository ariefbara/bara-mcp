<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\ProgramCompositionId,
    Application\Service\Firm\Program\RegistrationPhaseRepository,
    Domain\Model\Firm\Program\RegistrationPhase
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineRegistrationPhaseRepository extends EntityRepository implements RegistrationPhaseRepository
{

    public function add(RegistrationPhase $registrationPhase): void
    {
        $em = $this->getEntityManager();
        $em->persist($registrationPhase);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

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

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
