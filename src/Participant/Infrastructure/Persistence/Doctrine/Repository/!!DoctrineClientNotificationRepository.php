<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use User\ {
    Application\Listener\UserNotificationRepository,
    Domain\Model\User\UserNotification
};
use Doctrine\ORM\EntityRepository;
use Resources\Uuid;

class DoctrineUserNotificationRepository extends EntityRepository implements UserNotificationRepository
{

    public function add(UserNotification $userNotification): void
    {
        $em = $this->getEntityManager();
        $em->persist($userNotification);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
