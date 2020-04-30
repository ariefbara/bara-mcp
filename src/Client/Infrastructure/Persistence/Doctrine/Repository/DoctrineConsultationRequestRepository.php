<?php

namespace Client\Infrastructure\Persistence\Doctrine\Repository;

use Client\{
    Application\Service\Client\ProgramParticipation\ConsultationRequestRepository,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Domain\Model\Client\ProgramParticipation\ConsultationRequest
};
use Doctrine\ORM\{
    EntityRepository,
    NoResultException
};
use Resources\{
    Exception\RegularException,
    Infrastructure\Persistence\Doctrine\PaginatorBuilder,
    Uuid
};

class DoctrineConsultationRequestRepository extends EntityRepository implements ConsultationRequestRepository
{

    public function add(ConsultationRequest $consultationRequest): void
    {
        $em = $this->getEntityManager();
        $em->persist($consultationRequest);
        $em->flush();
    }

    public function all(
            ProgramParticipationCompositionId $programParticipationCompositionId, int $page, int $pageSize)
    {
        $parameters = [
            "clientId" => $programParticipationCompositionId->getClientId(),
            "programParticipationId" => $programParticipationCompositionId->getProgramParticipationId(),
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->leftJoin('consultationRequest.programParticipation', 'programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($parameters);

        return PaginatorBuilder::build($qb->getQuery(), $page, $pageSize);
    }

    public function nextIdentity(): string
    {
        return Uuid::generateUuid4();
    }

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationRequestId): ConsultationRequest
    {
        $parameters = [
            "clientId" => $programParticipationCompositionId->getClientId(),
            "programParticipationId" => $programParticipationCompositionId->getProgramParticipationId(),
            "consultationRequestId" => $consultationRequestId,
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.programParticipation', 'programParticipation')
                ->andWhere($qb->expr()->eq('programParticipation.active', 'true'))
                ->andWhere($qb->expr()->eq('programParticipation.id', ':programParticipationId'))
                ->leftJoin('programParticipation.client', 'client')
                ->andWhere($qb->expr()->eq('client.id', ':clientId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation request not found';
            throw RegularException::notFound($errorDetail);
        }
    }

    public function update(): void
    {
        $this->getEntityManager()->flush();
    }

    public function aConsultationRequestOfConsultant(
            string $firmId, string $personnelId, string $consultantId, string $consultationRequestId): ConsultationRequest
    {
        $parameters = [
            "consultationRequestId" => $consultationRequestId,
            "consultantId" => $consultantId,
            "personnelId" => $personnelId,
            "firmId" => $firmId,
        ];

        $qb = $this->createQueryBuilder('consultationRequest');
        $qb->select('consultationRequest')
                ->andWhere($qb->expr()->eq('consultationRequest.id', ':consultationRequestId'))
                ->leftJoin('consultationRequest.consultant', 'consultant')
                ->andWhere($qb->expr()->eq('consultant.removed', 'false'))
                ->andWhere($qb->expr()->eq('consultant.id', ':consultantId'))
                ->leftJoin('consultant.personnel', 'personnel')
                ->andWhere($qb->expr()->eq('personnel.removed', 'false'))
                ->andWhere($qb->expr()->eq('personnel.id', ':personnelId'))
                ->leftJoin('personnel.firm', 'firm')
                ->andWhere($qb->expr()->eq('firm.id', ':firmId'))
                ->setParameters($parameters)
                ->setMaxResults(1);

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $ex) {
            $errorDetail = 'not found: consultation request not found';
            throw RegularException::notFound($errorDetail);
        }
    }

}
