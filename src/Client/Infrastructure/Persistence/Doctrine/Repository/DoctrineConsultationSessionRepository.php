<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\{
    Application\Service\Client\ProgramParticipation\ConsultationSessionRepository,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Domain\Model\Client\ProgramParticipation\ConsultationSession
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder
};

class DoctrineConsultationSessionRepository extends EntityRepository implements ConsultationSessionRepository
{

    public function all(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize)
    {
        $parameters = [
            "clientId" => $programParticipationCompositionId->getClientId(),
            "programParticipationId" => $programParticipationCompositionId->getProgramParticipationId(),
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->leftJoin('consultationSession.programParticipation', 'programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($parameters);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationSessionId): ConsultationSession
    {
        $parameters = [
            "clientId" => $programParticipationCompositionId->getClientId(),
            "programParticipationId" => $programParticipationCompositionId->getProgramParticipationId(),
            "consultationSessionId" => $consultationSessionId,
        ];

        $qb = $this->createQueryBuilder('consultationSession');
        $qb->select('consultationSession')
                ->andWhere($qb->expr()->eq('consultationSession.id', ':consultationSessionId'))
                ->leftJoin('consultationSession.programParticipation', 'programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation session not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

}
