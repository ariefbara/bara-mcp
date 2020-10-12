<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Application\Service\Firm\Client\ClientNotificationRepository;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineClientNotificationRecipientRepository extends EntityRepository implements ClientNotificationRepository
{

    public function allNotificationsBelongsToClient(
            string $firmId, string $clientId, int $page, int $pageSize, ?bool $readStatus)
    {
        $params = [
            "firmId" => $firmId,
            "clientId" => $clientId,
        ];
        
        $qb = $this->createQueryBuilder("clientNotification");
        $qb->select("clientNotification")
                ->leftJoin("clientNotification.client", "client")
                ->andWhere($qb->expr()->eq("client.id", ":clientId"))
                ->leftJoin("client.firm", "firm")
                ->andWhere($qb->expr()->eq("firm.id", ":firmId"))
                ->orderBy("clientNotification.notifiedTime", "DESC")
                ->setParameters($params);
        
        if (isset($readStatus)) {
            $qb->andWhere($qb->expr()->eq("clientNotification.read", ":readStatus"))
                    ->setParameter("readStatus", $readStatus);
        }
        
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
