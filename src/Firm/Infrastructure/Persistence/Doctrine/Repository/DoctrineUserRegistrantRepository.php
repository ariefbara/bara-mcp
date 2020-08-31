<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\UserRegistrantRepository,
    Domain\Model\Firm\Program\UserRegistrant
};
use Resources\Exception\RegularException;

class DoctrineUserRegistrantRepository extends EntityRepository implements UserRegistrantRepository
{

    public function ofId(string $firmId, string $programId, string $userRegistrantId): UserRegistrant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'userRegistrantId' => $userRegistrantId,
        ];

        $qb = $this->createQueryBuilder('userRegistrant');
        $qb->select('userRegistrant')
                ->andWhere($qb->expr()->eq('userRegistrant.id', ':userRegistrantId'))
                ->leftJoin('userRegistrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: user registrant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
