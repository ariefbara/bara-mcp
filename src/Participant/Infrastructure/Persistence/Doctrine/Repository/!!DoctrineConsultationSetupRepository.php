<?php

namespace User\Infrastructure\Persistence\Doctrine\Repository;

use User\{
    Application\Service\Firm\Program\ConsultationSetupRepository,
    Domain\Model\User\ProgramParticipation,
    Domain\Model\Firm\Program\ConsultationSetup
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\Exception\RegularException;

class DoctrineConsultationSetupRepository extends EntityRepository implements ConsultationSetupRepository
{

    public function aConsultationSetupInProgramWhereUserParticipate(
            string $userId, string $programParticipationId, string $consultationSetupId): ConsultationSetup
    {
        $params = [
            "userId" => $userId,
            "programParticipationId" => $programParticipationId,
            "consultationSetupId" => $consultationSetupId,
        ];

        $subQuery = $this->getEntityManager()->createQueryBuilder();
        $subQuery->select('tprogram.id')
                ->from(ProgramParticipation::class, 'programParticipation')
                ->andWhere($subQuery->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($subQuery->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.program', 'tprogram')
                ->leftJoin('programParticipation.user', 'tuser')
                ->andWhere($subQuery->expr()->eq('tuser.id', ':userId'))
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
