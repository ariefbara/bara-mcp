<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Auth\Firm\ManagerRepository as InterfaceForAuth;
use Query\Application\Service\Firm\ManagerRepository;
use Query\Application\Service\Manager\ManagerRepository as ManagerRepository2;
use Query\Domain\Model\Firm\Manager;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineManagerRepository extends EntityRepository implements ManagerRepository, InterfaceForAuth, ManagerRepository2
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

    public function aManagerInFirm(string $firmId, string $managerId): Manager
    {
        $params = [
            'firmId' => $firmId,
            'managerId' => $managerId,
        ];
        
        $qb = $this->createQueryBuilder('manager');
        $qb->select('manager')
                ->andWhere($qb->expr()->eq('manager.id', ':managerId'))
                ->leftJoin('manager.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            throw RegularException::notFound('not found: manager not found');
        }
    }

}
