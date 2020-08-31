<?php

namespace Bara\Infrastructure\Persistence\Doctrine\Repository;

use Bara\ {
    Application\Service\UserRepository,
    Domain\Model\User
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineUserRepository extends EntityRepository implements UserRepository
{
    
    public function ofId(string $userId): User
    {
        $params = [
            'userId' => $userId,
        ];
        
        $qb = $this->createQueryBuilder('user');
        $qb->select('user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);
        
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
