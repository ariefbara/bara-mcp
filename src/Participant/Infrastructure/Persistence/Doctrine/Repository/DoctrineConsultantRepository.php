<?php

namespace Participant\Infrastructure\Persistence\Doctrine\Repository;

use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Participant\ {
    Application\Service\Participant\ConsultantRepository,
    Domain\Model\DependencyEntity\Firm\Program\Consultant
};
use Query\Domain\Model\Firm\Program;
use Resources\Exception\RegularException;

class DoctrineConsultantRepository extends EntityRepository implements ConsultantRepository
{

    public function ofId(string $firmId, string $programId, string $personnelId): Consultant
    {
        $params = [
            'firmId' => $firmId,
            'programId' => $programId,
            'personnelId' => $personnelId,
        ];

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.personnelId', ':personnelId'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->eq('program.id', ':programId'))
                ->andWhere($qb->expr()->eq('program.firmId', ':firmId'))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
