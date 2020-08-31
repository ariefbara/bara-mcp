<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\ClientRegistrantRepository,
    Domain\Model\Firm\Program\ClientRegistrant
};
use Resources\Exception\RegularException;

class DoctrineClientRegistrantRepository extends EntityRepository implements ClientRegistrantRepository
{

    public function ofId(string $firmId, string $programId, string $clientRegistrantId): ClientRegistrant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'clientRegistrantId' => $clientRegistrantId,
        ];

        $qb = $this->createQueryBuilder('clientRegistrant');
        $qb->select('clientRegistrant')
                ->andWhere($qb->expr()->eq('clientRegistrant.id', ':clientRegistrantId'))
                ->leftJoin('clientRegistrant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->leftJoin('program.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: registrant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
