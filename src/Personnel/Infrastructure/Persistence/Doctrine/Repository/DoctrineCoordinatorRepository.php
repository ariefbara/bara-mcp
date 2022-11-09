<?php

namespace Personnel\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\NoResultException;
use Personnel\Application\Service\Firm\Personnel\Coordinator\CoordinatorRepository;
use Personnel\Domain\Model\Firm\Personnel\Coordinator;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\Repository\DoctrineEntityRepository;

class DoctrineCoordinatorRepository extends DoctrineEntityRepository implements CoordinatorRepository
{

    public function aCoordinatorBelongsToPersonnel(string $personnelId, string $coordinatorId): Coordinator
    {
        $parameters = [
            'personnelId' => $personnelId,
            'coordinatorId' => $coordinatorId,
        ];

        $qb = $this->createQueryBuilder('coordinator');
        $qb->select('coordinator')
                ->andWhere($qb->expr()->eq('coordinator.id', ':coordinatorId'))
                ->leftJoin('coordinator.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('coordinator not found');
        }
    }

}
