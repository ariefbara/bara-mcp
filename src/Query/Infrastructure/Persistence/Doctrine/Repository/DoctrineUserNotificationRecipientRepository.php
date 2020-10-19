<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Query\Application\Service\User\UserNotificationRepository;
use Resources\Infrastructure\Persistence\Doctrine\PaginatorBuilder;

class DoctrineUserNotificationRecipientRepository extends EntityRepository implements UserNotificationRepository
{
    
    public function allNotificationBelongsToUser(string $userId, int $page, int $pageSize, ?bool $readStatus)
    {
        $params = [
            "userId" => $userId,
        ];
        
        $qb = $this->createQueryBuilder("userNotification");
        $qb->select("userNotification")
                ->leftJoin("userNotification.user", "user")
                ->andWhere($qb->expr()->eq("user.id", ":userId"))
                ->orderBy("userNotification.notifiedTime", "DESC")
                ->setParameters($params);
        
        if (isset($readStatus)) {
            $qb->andWhere($qb->expr()->eq("userNotification.read", ":read"))
                    ->setParameter("read", $readStatus);
        }
        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

}
