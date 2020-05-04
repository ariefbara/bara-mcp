<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Query\ {
    Application\Service\Client\ClientNotificationRepository,
    Domain\Model\Client\ClientNotification
};
use Resources\ {
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineClientNotificationRepository extends EntityRepository implements ClientNotificationRepository
{

    public function all(string $clientId, int $page, int $pageSize)
    {
        $params = [
            "clientId" =>  $clientId,
        ];
        
        $qb = $this->createQueryBuilder('clientNotification');
        $qb->select('clientNotification')
                ->leftJoin('clientNotification.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params);
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
        
    }

    public function ofId(string $clientId, string $clientNotificationId): ClientNotification
    {
        $params = [
            "clientNotificationId" =>  $clientNotificationId,
            "clientId" =>  $clientId,
        ];
        
        $qb = $this->createQueryBuilder('clientNotification');
        $qb->select('clientNotification')
                ->andWhere($qb->expr()->eq('clientNotification.id', ':clientNotificationId'))
                ->leftJoin('clientNotification.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: client notification not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
