<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\ {
    Application\Service\User\UserMailRepository,
    Domain\Model\User\UserMail
};
use Resources\Uuid;

class DoctrineUserMailRepository extends EntityRepository implements UserMailRepository
{
    
    public function add(UserMail $userMail): void
    {
        $em = $this->getEntityManager();
        $em->persist($userMail);
        $em->flush();
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

}
