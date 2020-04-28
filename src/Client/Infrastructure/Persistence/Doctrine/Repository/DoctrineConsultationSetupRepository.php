<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\ {
    Application\Service\Firm\Program\ConsultationSetupRepository,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Firm\Program\ConsultationSetup
};
use Doctrine\ORM\ {
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineConsultationSetupRepository extends EntityRepository implements ConsultationSetupRepository
{

    public function ofId(string $clientId, string $programParticipationId, string $consultationSetupId): ConsultationSetup
    {
        $params = [
            "clientId" => $clientId,
            "programParticipationId" => $programParticipationId,
            "consultationSetupId" => $consultationSetupId,
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

        $qb = $this->createQueryBuilder('consultationSetup');
        $qb->select('consultationSetup')
                ->andWhere($qb->expr()->eq('consultationSetup.removed', 'false'))
                ->andWhere($qb->expr()->eq('consultationSetup.id', ':consultationSetupId'))
                ->leftJoin('consultationSetup.program', 'program')
                ->andWhere($qb->expr()->in('program.id', $subQuery->getDQL()))
                ->setParameters($params)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation setup not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
