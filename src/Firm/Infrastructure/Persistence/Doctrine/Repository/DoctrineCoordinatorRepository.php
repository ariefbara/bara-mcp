<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Firm\{
    Application\Auth\Program\CoordinatorRepository as InterfaceForAuthorization,
    Application\Service\Firm\Program\CoordinatorRepository,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Program\Coordinator
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineCoordinatorRepository extends EntityRepository implements CoordinatorRepository, InterfaceForAuthorization
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

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('coordinator');
        $qb->select('coordinator')
                ->andWhere($qb->expr()->eq('coordinator.removed', 'false'))
                ->leftJoin('coordinator.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->setParameter('programId', $programCompositionId->getProgramId())
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameter('firmId', $programCompositionId->getFirmId());

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfUnremovedCoordinatorCorrespondWithPersonnel(
            string $firmId, string $personnelId, string $programId): bool
    {
        $parameters = [
            "programId" => $programId,
            "personnelId" => $personnelId,
            "firmId" => $firmId,
        ];
        
        $qb = $this->createQueryBuilder('coordinator');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('coordinator.removed', 'false'))
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('coordinator.program', 'program')
                ->andWhere($qb->expr()->eq('program.removed', 'false'))
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        return !empty($qb->getQuery()->getResult());
    }

}
