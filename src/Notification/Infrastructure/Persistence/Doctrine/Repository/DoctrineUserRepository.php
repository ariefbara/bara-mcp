<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\{
    Application\Service\User\UserRepository,
    Domain\Model\User
};

class DoctrineUserRepository extends EntityRepository implements UserRepository
{

    public function ofId(string $userId): User
    {
        return $this->findOneBy(["id" => $userId]);
    }

}
