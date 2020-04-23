<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Auth\ManagerRepository as InterfaceForAuthorization,
    Application\Service\Firm\ManagerRepository,
    Domain\Model\Firm\Manager
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder,
    Uuid
};

class DoctrineManagerRepository extends EntityRepository implements ManagerRepository, InterfaceForAuthorization
{

    public function add(Manager $manager): void
    {
        $em = $this->getEntityManager();
        $em->persist($manager);
        $em->flush();
    }

    public function all(string $firmId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
        ];
        
        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
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

    public function ofEmail(string $firmIdentifier, string $email): Manager
    {
        $params = [
            "firmIdentifier" => $firmIdentifier,
            "email" => $email,
        ];
        
        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.email', ":email"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.identifier', ":firmIdentifier"))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
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

    public function containRecordOfId(string $firmId, string $managerId): bool
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];
        
        $qb = $this->createQueryBuilder('manager');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('manager.id', ":managerId"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($params)
                ->setMaxResults(1);
        return !empty($qb->getQuery()->getResult());
    }

}
