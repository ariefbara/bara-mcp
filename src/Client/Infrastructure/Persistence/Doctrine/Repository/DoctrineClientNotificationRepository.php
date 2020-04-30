<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Client\ClientNotificationRepository,
    Domain\Model\Client\ClientNotification
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineClientNotificationRepository extends EntityRepository implements ClientNotificationRepository
{
    
    public function all(string $clientId, int $page, int $pageSize)
    {
        $parameters = [
            "clientId" => $clientId,
        ];
        
        $qb = $this->createQueryBuilder('clientNotification');
        $qb->select('clientNotification')
                ->leftJoin("clientNotification.client", "client")
                ->andWhere($qb->expr()->eq('client.id', ":clientId"))
                ->setParameters($parameters);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(string $clientId, string $clientNotificationId): ClientNotification
    {
        $parameters = [
            "clientNotificationId" => $clientNotificationId,
            "clientId" => $clientId,
        ];
        
        $qb = $this->createQueryBuilder('clientNotification');
        $qb->select('clientNotification')
                ->andWhere($qb->expr()->eq('clientNotification.id', ":clientNotificationId"))
                ->leftJoin("clientNotification.client", "client")
                ->andWhere($qb->expr()->eq('client.id', ":clientId"))
                ->setParameters($parameters)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = "not found: client notification not found";
            throw RegularException::notFound($errorDetail);
        }
    }

}
