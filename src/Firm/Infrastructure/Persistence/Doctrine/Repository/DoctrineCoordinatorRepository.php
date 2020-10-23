<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\CoordinatorRepository,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Program\Coordinator
};
use Resources\Exception\RegularException;

class DoctrineCoordinatorRepository extends EntityRepository implements CoordinatorRepository
{

    public function ofId(ProgramCompositionId $programCompositionId, string $coordinatorId): Coordinator
    {
        $qb = $this->createQueryBuilder('coordinator');
        $qb->select('coordinator')
                ->andWhere($qb->expr()->eq('coordinator.removed', 'false'))
                ->andWhere($qb->expr()->eq('coordinator.id', ':coordinatorId'))
                ->setParameter('coordinatorId', $coordinatorId)
                ->leftJoin('coordinator.program', 'program')
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
            $errorDetail = "not found: coordinator not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aCoordinatorCorrespondWithPersonnel(string $programId, string $personnelId): Coordinator
    {
        $params = [
            "programId" => $programId,
            "personnelId" => $personnelId,
        ];
        
        $qb = $this->createQueryBuilder("coordinator");
        $qb->select("coordinator")
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("coordinator.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: coordinator not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
