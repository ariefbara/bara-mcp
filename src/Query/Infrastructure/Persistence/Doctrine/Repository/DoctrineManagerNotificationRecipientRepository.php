<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Query\Application\Service\Firm\Manager\ManagerNotificationRecipientRepository;
use Query\Domain\Model\Firm\Manager\ManagerNotificationRecipient;
use Resources\Exception\RegularException;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineManagerNotificationRecipientRepository extends EntityRepository implements ManagerNotificationRecipientRepository
{
    
    public function aNotificationForManager(string $firmId, string $managerId, string $managerNotificationId): ManagerNotificationRecipient
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
            "managerNotificationId" => $managerNotificationId,
        ];
        
        $qb = $this->createQueryBuilder("managerNotification");
        $qb->select("managerNotification")
                ->andWhere($qb->expr()->eq("managerNotification.id", ":managerNotificationId"))
                ->leftJoin("managerNotification.manager", ":manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", ":firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setMaxResults(1)
                ->setParameters($params);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: manager notification not found";
            throw RegularException::notFound($errorDetail);
        }
    }

    public function allNotificationForManager(string $firmId, string $managerId, int $page, int $pageSize)
    {
        $params = [
            "firmId" => $firmId,
            "managerId" => $managerId,
        ];
        
        $qb = $this->createQueryBuilder("managerNotification");
        $qb->select("managerNotification")
                ->leftJoin("managerNotification.manager", ":manager")
                ->andWhere($qb->expr()->eq("manager.id", ":managerId"))
                ->leftJoin("manager.firm", ":firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
