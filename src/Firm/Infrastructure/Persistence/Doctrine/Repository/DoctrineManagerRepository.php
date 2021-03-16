<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\ManagerRepository,
    Domain\Model\Firm\Manager
};
use Resources\ {
    Exception\RegularException,
    Uuid
};

class DoctrineManagerRepository extends EntityRepository implements ManagerRepository
{

    public function add(Manager $manager): void
    {
        $em = $this->getEntityManager();
        $em->persist($manager);
        $em->flush();
    }

    public function isEmailAvailable(string $firmId, string $email): bool
    {
        $params = [
            "firmId" => $firmId,
            "email" => $email,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('manager.email', ":email"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        return empty($qb->getQuery()->getResult());
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(string $firmId, string $managerId): Manager
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.id', ":managerId"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function aManagerOfId(string $managerId): Manager
    {
        $manager = $this->findOneBy(["id" => $managerId]);
        if (empty($manager)) {
            $errorDetail = "not found: manager not found";
            throw RegularException::forbidden($errorDetail);
        }
        return $manager;
    }

    public function aManagerInFirm(string $firmId, string $managerId): Manager
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.id', ":managerId"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
