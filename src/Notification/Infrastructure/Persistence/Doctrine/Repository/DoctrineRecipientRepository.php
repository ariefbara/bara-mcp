<?php

namespace Notification\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Notification\Application\Service\RecipientRepository;

class DoctrineRecipientRepository extends EntityRepository implements RecipientRepository
{
    
    public function allRecipientsWithZeroAttempt()
    {
        $criteria = [
            "attempt" => 0,
        ];
        return $this->findBy($criteria);
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
