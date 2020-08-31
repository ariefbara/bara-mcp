<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\UserParticipantRepository,
    Domain\Model\Firm\Program\UserParticipant
};
use Resources\Exception\RegularException;

class DoctrineUserParticipantRepository extends EntityRepository implements UserParticipantRepository
{

    public function ofId(string $firmId, string $programId, string $userId): UserParticipant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'userId' => $userId,
        ];

        $qb = $this->createQueryBuilder('userParticipant');
        $qb->select('userParticipant')
                ->leftJoin('userParticipant.user', 'user')
                ->andWhere($qb->expr()->eq('user.id', ':userId'))
                ->leftJoin('userParticipant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user participant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
