<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Auth\Firm\ManagerRepository as InterfaceForAuth,
    Application\Service\Firm\ManagerRepository,
    Domain\Model\Firm\Manager
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineManagerRepository extends EntityRepository implements ManagerRepository, InterfaceForAuth
{

    public function all(string $firmId, int $page, int $pageSize)
    {
        $parameters = [
            "firmId" => $firmId,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function containRecordOfId(string $firmId, string $managerId): bool
    {
        $parameters = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('1')
                ->andWhere($qb->expr()->eq('manager.id', ":managerId"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters)
                ->setMaxResults(1);
        return !empty($qb->getQuery()->getResult());
    }

    public function ofEmail(string $firmIdentifier, string $email): Manager
    {
        $parameters = [
            "firmIdentifier" => $firmIdentifier,
            "email" => $email,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.email', ":email"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.identifier', ":firmIdentifier"))
                ->setParameters($parameters)
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
        $parameters = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];

        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.id', ":managerId"))
                ->andWhere($qb->expr()->eq('manager.removed', "false"))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ":firmId"))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
