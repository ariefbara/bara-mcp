<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\{
    Application\Service\Firm\Program\ConsultantRepository,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Firm\Program\Consultant
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineConsultantRepository extends EntityRepository implements ConsultantRepository
{

    public function aConsultantInProgramWhereClientParticipate(
            string $clientId, string $programParticipationId, string $consultantId): Consultant
    {
        $parameters = [
            "clientId" => $clientId,
            "programParticipationId" => $programParticipationId,
            "consultantId" => $consultantId,
        ];

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tprogram.id')
                ->from(ProgramParticipation::class, 'programParticipation')
                ->andWhere($subQuery->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($subQuery->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.program', 'tprogram')
                ->leftJoin('programParticipation.client', 'tclient')
                ->andWhere($subQuery->expr()->eq('tclient.id', ':clientId'))
                ->setMaxResults(1);

        $qb = $this->createQueryBuilder('consultant');
        $qb->select('consultant')
                ->andWhere($qb->expr()->eq('consultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.program', 'program')
                ->andWhere($qb->expr()->in('program.id', $subQuery->getDQL()))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultant not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
