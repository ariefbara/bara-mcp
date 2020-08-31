<?php

namespace Query\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Query\{
    Application\Service\User\UserFileInfoRepository,
    Domain\Model\User\UserFileInfo
};
use Resources\Exception\RegularException;

class DoctrineUserFileInfoRepository extends EntityRepository implements UserFileInfoRepository
{

    public function ofId(string $userId, string $userFileInfoId): UserFileInfo
    {
        $params = [
            'userId' => $userId,
            'userFileInfoId' => $userFileInfoId,
        ];

        $qb = $this->createQueryBuilder('userFileInfo');
        $qb->select('userFileInfo')
                ->andWhere($qb->expr()->eq('userFileInfo.id', ':userFileInfoId'))
                ->leftJoin('userFileInfo.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user file info not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
