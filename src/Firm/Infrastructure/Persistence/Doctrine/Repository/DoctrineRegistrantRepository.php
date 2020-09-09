<?php

namespace Firm\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Firm\ {
    Application\Service\Firm\Program\RegistrantRepository,
    Domain\Model\Firm\Program\Registrant
};
use Resources\Exception\RegularException;

class DoctrineRegistrantRepository extends EntityRepository implements RegistrantRepository
{

    public function ofId(string $firmId, string $programId, string $registrantId): Registrant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'registrantId' => $registrantId,
        ];

        $qb = $this->createQueryBuilder('registrant');
        $qb->select('registrant')
                ->andWhere($qb->expr()->eq('registrant.id', ':registrantId'))
                ->leftJoin('registrant.program', 'program')
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
