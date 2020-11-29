<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Coordinator\CoordinatorRepository as InterfaceForCoordinator,
    Application\Service\Firm\Program\CoordinatorRepository,
    Application\Service\Firm\Program\ProgramCompositionId,
    Application\Service\Manager\CoordinatorRepository as InterfaceForManager,
    Domain\Model\Firm\Program\Coordinator
};
use Resources\Exception\RegularException;

class DoctrineCoordinatorRepository extends EntityRepository implements CoordinatorRepository, InterfaceForCoordinator, InterfaceForManager
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

    public function aCoordinatorCorrespondWithProgram(string $firmId, string $personnelId, string $programId): Coordinator
    {
        $params = [
            "firmId" => $firmId,
            "personnelId" => $personnelId,
            "programId" => $programId,
        ];

        $qb = $this->createQueryBuilder("coordinator");
        $qb->select("coordinator")
                ->leftJoin("coordinator.program", "program")
                ->andWhere($qb->expr()->eq("program.id", ":programId"))
                ->leftJoin("coordinator.personnel", "personnel")
                ->andWhere($qb->expr()->eq("personnel.id", ":personnelId"))
                ->leftJoin("personnel.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: program coordinator not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aCoordinatorOfId(string $coordinatorId): Coordinator
    {
        $coordinator = $this->findOneBy(["id" => $coordinatorId]);
        if (empty($coordinator)) {
            $errorDetail = "not found: coordinator not found";
            throw RegularException::notFound($errorDetail);
        }
        return $coordinator;
    }

}
