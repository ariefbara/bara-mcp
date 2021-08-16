<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\Application\Auth\Firm\Program\CoordinatorRepository as InterfaceForAuth;
use Query\Application\Service\Coordinator\CoordinatorRepository as InterfaceForCoordinator;
use Query\Application\Service\Firm\Program\CoordinatorRepository;
use Query\Domain\Model\Firm\Program\Coordinator;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineCoordinatorRepository extends EntityRepository implements CoordinatorRepository, InterfaceForAuth, InterfaceForCoordinator
{

    public function ofId(ProgramCompositionId $programCompositionId, string $coordinatorId): Coordinator
    {
        $qb = $this->createQueryBuilder('coordinator');
        $qb->select('coordinator')
                ->andWhere($qb->expr()->eq('coordinator.id', ':coordinatorId'))
                ->setParameter('coordinatorId', $coordinatorId)
                ->leftJoin('coordinator.program', 'program')
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

    public function all(ProgramCompositionId $programCompositionId, int $page, int $pageSize)
    {
        $qb = $this->createQueryBuilder('coordinator');
        $qb->select('coordinator')
                ->andWhere($qb->expr()->eq('coordinator.active', 'true'))
                ->leftJoin('coordinator.program', 'program')
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
                ->andWhere($qb->expr()->eq('coordinator.active', 'true'))
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('coordinator.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        return !empty($qb->getQuery()->getResult());
    }

    public function aCoordinatorCorrespondWithProgram(string $firmId, string $personnelId, string $programId): Coordinator
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'programId' => $programId,
        ];
        $qb = $this->createQueryBuilder('coordinator');
        $qb->select('coordinator')
                ->leftJoin('coordinator.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: coordinator not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aCoordinatorBelongsToPersonnel(string $firmId, string $personnelId, string $coordinatorId): Coordinator
    {
        $params = [
            'firmId' => $firmId,
            'personnelId' => $personnelId,
            'coordinatorId' => $coordinatorId,
        ];
        
        $qb = $this->createQueryBuilder('coordinator');
        $qb->select('coordinator')
                ->andWhere($qb->expr()->eq('coordinator.id', ':coordinatorId'))
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: coordinator not found');
        }
    }

}
